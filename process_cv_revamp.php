<?php
// Start output buffering to prevent any accidental output
ob_start();

header('Content-Type: application/json');

// Enable error reporting for debugging but don't display errors
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Error handler to convert errors to JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_clean(); // Clear any output
    echo json_encode([
        'success' => false,
        'error' => "PHP Error: $errstr in $errfile on line $errline"
    ]);
    exit;
});

// Load Composer autoloader for dependencies (PDF parser, etc.)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Load configuration if exists
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} elseif (file_exists(__DIR__ . '/config.example.php')) {
    require_once __DIR__ . '/config.example.php';
}

// Load PHPMailer
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load TCPDF if available (Composer or manual installation)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/tcpdf/tcpdf.php')) {
    require_once __DIR__ . '/tcpdf/tcpdf.php';
} elseif (file_exists(__DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php')) {
    require_once __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php';
}

function json_error($message) {
    ob_clean(); // Clear any output buffer
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Invalid request method');
}

// Validate required fields (either file upload OR pasted text)
if (empty($_POST['target_job']) || empty($_POST['email'])) {
    json_error('Please fill in all required fields');
}

// Check if either file upload or pasted text is provided
if (empty($_FILES['current_cv']['tmp_name']) && empty($_POST['cv_text'])) {
    json_error('Please either upload a CV file or paste your CV text');
}

// Validate email
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    json_error('Invalid email address');
}

// Create uploads directory if it doesn't exist
$upload_dir = defined('CV_UPLOAD_DIR') ? CV_UPLOAD_DIR : __DIR__ . '/data/cv_uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate unique ID for this request
$unique_id = uniqid('cv_', true);

// Check if using pasted text or file upload
$cv_text = '';

if (!empty($_POST['cv_text'])) {
    // User pasted CV text
    $cv_text = trim($_POST['cv_text']);
    
    if (strlen($cv_text) < 100) {
        json_error('Please paste your complete CV text (at least 100 characters)');
    }
    
} else {
    // User uploaded a file
    if (!isset($_FILES['current_cv']) || $_FILES['current_cv']['error'] !== UPLOAD_ERR_OK) {
        json_error('Please either upload a CV file or paste your CV text');
    }
    
    $cv_file = $_FILES['current_cv'];
    $allowed_extensions = ['pdf', 'doc', 'docx'];
    $file_extension = strtolower(pathinfo($cv_file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        json_error('Invalid file format. Please upload PDF, DOC, or DOCX');
    }

    if ($cv_file['size'] > 10 * 1024 * 1024) { // 10MB limit
        json_error('File size exceeds 10MB limit');
    }

    // Generate filename
    $cv_filename = $unique_id . '.' . $file_extension;
    $cv_path = $upload_dir . '/' . $cv_filename;

    // Move uploaded file
    if (!move_uploaded_file($cv_file['tmp_name'], $cv_path)) {
        json_error('Failed to upload file');
    }

    // Extract text from CV
    $cv_text = extractTextFromCV($cv_path, $file_extension);
    
    // Clean up uploaded file
    @unlink($cv_path);
}

// Validate extracted/pasted text
$cv_text_cleaned = trim($cv_text);
if (empty($cv_text_cleaned) || strlen($cv_text_cleaned) < 50) {
    // Only show file-specific error if they uploaded a file
    if (!empty($_POST['cv_text'])) {
        json_error('The pasted CV text is too short. Please paste your complete CV (at least 50 characters).');
    }
    
    // File extraction error
    $error_msg = 'Could not extract readable text from your CV. ';
    
    if (isset($file_extension)) {
        if ($file_extension === 'pdf') {
            $error_msg .= 'The PDF may be scanned/image-based or protected. Please try: ';
            $error_msg .= '1) Click "Paste Text" tab above and copy-paste your CV content, or 2) Save as DOCX and try again.';
        } elseif ($file_extension === 'doc') {
            $error_msg .= 'Old DOC format may have compatibility issues. Please try: ';
            $error_msg .= '1) Click "Paste Text" tab and paste your CV, or 2) Save as DOCX.';
        } else {
            $error_msg .= 'Click "Paste Text" tab above and paste your CV content instead.';
        }
    }
    
    json_error($error_msg);
}

// Get job details
$target_job = htmlspecialchars(trim($_POST['target_job']));
$job_description = !empty($_POST['job_description']) ? htmlspecialchars(trim($_POST['job_description'])) : '';

// Generate revamped CV using AI
$revamped_cv = generateRevampedCV($cv_text_cleaned, $target_job, $job_description);

if (!$revamped_cv) {
    json_error('Failed to generate revamped CV. Please try again.');
}

// Save revamped CV as PDF (or TXT if TCPDF unavailable)
$file_extension = class_exists('TCPDF') ? 'pdf' : 'txt';
$output_filename = 'revamped_cv_' . $unique_id . '.' . $file_extension;
$output_path = $upload_dir . '/' . $output_filename;

// Generate PDF using TCPDF
$pdf_created = createPDF($revamped_cv, $output_path, $target_job, $email);

if (!$pdf_created) {
    json_error('Failed to create PDF. The revamped content has been generated but could not be saved.');
}

// Save metadata
$metadata = [
    'id' => $unique_id,
    'email' => $email,
    'target_job' => $target_job,
    'job_description' => $job_description,
    'original_cv' => isset($cv_file['name']) ? $cv_file['name'] : 'pasted_text',
    'timestamp' => date('Y-m-d H:i:s'),
    'output_file' => $output_filename
];

file_put_contents($upload_dir . '/metadata_' . $unique_id . '.json', json_encode($metadata, JSON_PRETTY_PRINT));

// Send email with revamped CV
sendRevampedCVEmail($email, $output_path, $target_job);

// Return success response
$message = class_exists('TCPDF') 
    ? 'Your CV has been revamped successfully! Check your email for the PDF download link.'
    : 'Your CV has been revamped successfully! Download link ready (Note: Install TCPDF for PDF format).';

ob_clean(); // Clear any output buffer before sending JSON
echo json_encode([
    'success' => true,
    'message' => $message,
    'download_url' => 'download_file.php?file=' . urlencode($output_filename) . '&type=cv',
    'file_type' => $file_extension
]);

/**
 * Extract text from CV file
 */
function extractTextFromCV($filepath, $extension) {
    $text = '';
    $extraction_method = '';
    
    if ($extension === 'pdf') {
        // Method 1: Try PDF parser library if available
        if (class_exists('Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filepath);
                $text = $pdf->getText();
                $extraction_method = 'PDF Parser Library';
                
                if (!empty(trim($text))) {
                    error_log("CV extraction successful using: $extraction_method");
                }
            } catch (Exception $e) {
                error_log('PDF parsing error: ' . $e->getMessage());
                $text = '';
            }
        }
        
        // Method 2: Try pdftotext command if library failed
        if (empty(trim($text))) {
            $output = @shell_exec("pdftotext " . escapeshellarg($filepath) . " - 2>&1");
            if (!empty($output) && 
                strpos($output, 'not recognized') === false && 
                strpos($output, 'not found') === false &&
                strpos($output, 'command not found') === false) {
                $text = $output;
                $extraction_method = 'pdftotext command';
                error_log("CV extraction successful using: $extraction_method");
            }
        }
        
        // Method 3: Manual extraction from raw PDF
        if (empty(trim($text))) {
            $raw = file_get_contents($filepath);
            $text = extractReadableTextFromPDF($raw);
            $extraction_method = 'Manual PDF extraction';
            if (!empty(trim($text))) {
                error_log("CV extraction successful using: $extraction_method");
            }
        }
        
    } elseif ($extension === 'docx') {
        // Extract from DOCX (ZIP archive with XML)
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($filepath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                if ($xml) {
                    $xml_obj = @simplexml_load_string($xml);
                    if ($xml_obj) {
                        $text = strip_tags($xml_obj->asXML());
                        $extraction_method = 'DOCX XML extraction';
                        error_log("CV extraction successful using: $extraction_method");
                    }
                }
            }
        }
        
    } elseif ($extension === 'doc') {
        // For old DOC files, try antiword first
        $output = @shell_exec("antiword " . escapeshellarg($filepath) . " 2>&1");
        if (!empty($output) && 
            strpos($output, 'not recognized') === false &&
            strpos($output, 'not found') === false) {
            $text = $output;
            $extraction_method = 'antiword command';
            error_log("CV extraction successful using: $extraction_method");
        }
        
        // Fallback: extract readable text from binary DOC
        if (empty(trim($text))) {
            $raw = file_get_contents($filepath);
            $text = extractReadableText($raw);
            $extraction_method = 'Manual DOC extraction';
            if (!empty(trim($text))) {
                error_log("CV extraction successful using: $extraction_method");
            }
        }
    }
    
    // Clean up the extracted text
    if (!empty($text)) {
        $text = cleanExtractedText($text);
        $cleaned_length = strlen(trim($text));
        error_log("Final cleaned text length: $cleaned_length characters");
        
        if ($cleaned_length < 50) {
            error_log("Warning: Extracted text is very short ($cleaned_length chars)");
        }
    } else {
        error_log("Error: No text could be extracted from CV file: $filepath (extension: $extension)");
    }
    
    return trim($text);
}

/**
 * Extract readable text from raw PDF content
 */
function extractReadableTextFromPDF($raw_content) {
    $text = '';
    
    // Method 1: Extract text between BT (Begin Text) and ET (End Text) markers
    if (preg_match_all('/BT\s+(.*?)\s+ET/s', $raw_content, $matches)) {
        foreach ($matches[1] as $text_block) {
            // Extract text from Tj operators
            if (preg_match_all('/\((.*?)\)\s*Tj/s', $text_block, $text_matches)) {
                foreach ($text_matches[1] as $line) {
                    $text .= $line . "\n";
                }
            }
            // Extract text from TJ operators (array)
            if (preg_match_all('/\[(.*?)\]\s*TJ/s', $text_block, $array_matches)) {
                foreach ($array_matches[1] as $array_content) {
                    if (preg_match_all('/\((.*?)\)/', $array_content, $array_text)) {
                        $text .= implode(' ', $array_text[1]) . "\n";
                    }
                }
            }
        }
    }
    
    // Method 2: If no text found, extract all printable text
    if (empty(trim($text))) {
        // Remove binary chunks and keep readable text
        $text = preg_replace('/stream[\r\n]+.*?endstream/s', '', $raw_content);
        $text = preg_replace('/<<[^>]*>>/', '', $text);
        $text = preg_replace('/\d+ \d+ obj/', '', $text);
        $text = preg_replace('/endobj/', '', $text);
        $text = preg_replace('/[^\x20-\x7E\n\r]/', ' ', $text);
    }
    
    return $text;
}

/**
 * Extract readable text from any binary format
 */
function extractReadableText($content) {
    // Remove common binary patterns
    $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', ' ', $content);
    // Keep only printable ASCII and newlines
    $content = preg_replace('/[^\x20-\x7E\n\r]/', '', $content);
    return $content;
}

/**
 * Clean extracted text from PDF artifacts
 */
function cleanExtractedText($text) {
    // Remove PDF object markers
    $text = preg_replace('/\d+\s+\d+\s+obj\s*/', '', $text);
    $text = preg_replace('/endobj\s*/', '', $text);
    $text = preg_replace('/endstream\s*/', '', $text);
    $text = preg_replace('/stream\s*/', '', $text);
    
    // Remove PDF dictionary markers
    $text = preg_replace('/<</[^>]*>>/s', '', $text);
    $text = preg_replace('/<<|>>/', '', $text);
    
    // Remove PDF operators and references
    $text = preg_replace('/\/[A-Z][a-zA-Z0-9]*/', '', $text);
    $text = preg_replace('/\d+\s+\d+\s+R/', '', $text);
    
    // Remove encoding artifacts
    $text = preg_replace('/\/Filter.*?Length\s+\d+/', '', $text);
    $text = preg_replace('/\/FlateDecode/', '', $text);
    
    // Clean up whitespace
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $text);
    
    // Remove lines that are just numbers or single characters
    $lines = explode("\n", $text);
    $cleaned_lines = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (strlen($line) > 2 && !preg_match('/^[\d\s]+$/', $line)) {
            $cleaned_lines[] = $line;
        }
    }
    
    return implode("\n", $cleaned_lines);
}

/**
 * Generate revamped CV using AI with strict constraints
 */
function generateRevampedCV($cv_text, $target_job, $job_description) {
    // AI API configuration
    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : getenv('OPENAI_API_KEY');
    $use_ai = defined('ENABLE_AI_PROCESSING') ? ENABLE_AI_PROCESSING : true;
    
    if (!$use_ai || empty($api_key) || $api_key === 'your-api-key-here') {
        // Fallback to rule-based revamp if no API key
        return ruleBasedRevamp($cv_text, $target_job, $job_description);
    }
    
    // Build strict prompt
    $prompt = buildRevampPrompt($cv_text, $target_job, $job_description);
    
    // Call AI API
    $api_endpoint = defined('OPENAI_ENDPOINT') ? OPENAI_ENDPOINT : 'https://api.openai.com/v1/chat/completions';
    $model = defined('OPENAI_MODEL') ? OPENAI_MODEL : 'gpt-4';
    $temperature = defined('CV_TEMPERATURE') ? CV_TEMPERATURE : 0.3;
    $max_tokens = defined('CV_MAX_TOKENS') ? CV_MAX_TOKENS : 2000;
    
    $ch = curl_init($api_endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an HR Career Expert and Selection Panel Assessor. Rewrite resumes to align perfectly with the target job description. Use ONLY facts from the provided resume; never fabricate. Optimize for ATS with JD keywords, highlight measurable impact and leadership, and keep a human, natural tone that passes AI detection. Ensure clear ATS-friendly structure and formatting.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => $temperature,
            'max_tokens' => $max_tokens
        ])
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        return ruleBasedRevamp($cv_text, $target_job, $job_description);
    }
    
    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? null;
}

/**
 * Build AI prompt with strict no-hallucination constraints
 */
function buildRevampPrompt($cv_text, $target_job, $job_description) {
    $jd_section = !empty($job_description) 
        ? "\n\nTarget Job Description (use for keywords/criteria):\n$job_description\n"
        : "";
    
    return <<<PROMPT
Act as an HR Career Expert and Selection Panel Assessor. Rewrite the enclosed resume so it aligns perfectly with the $target_job role.

Rules (non-negotiable):
1) Fact-Based Only: use only information in the resume. No fabrication. If criteria are missing, surface transferable skills from real roles.
2) ATS Compliance: weave in keywords/phrases from the job description naturally; ATS-friendly headings, bullets, and consistent formatting.
3) High-Impact Presentation: position the candidate as a high-achieving corporate asset; emphasize measurable outcomes (KPI %, cost/time savings, efficiency gains, revenue/quality improvements, leadership impact).
4) Alignment to Job Description: map experience and skills to essential/desirable criteria; emphasize relevant tech/skills/achievements; show adaptability via transferable skills where gaps exist.
5) Humanized & Professional Tone: natural, authentic voice that passes AI detection; avoid robotic phrasing or generic buzzwords; clear and concise.
6) Structure & Formatting (ATS-friendly):
   - Header with name/contact
   - Professional Summary (tailored, keyword-rich)
   - Key Skills (aligned with JD)
   - Work Experience (reverse chronological; quantified bullets with STAR-style results)
   - Education & Certifications
   - Optional: Projects, Awards, Technical Proficiencies (only if in source)
7) Success Metrics: each role should include 2–3 quantified achievements (e.g., “Reduced resolution time by 35%”).
8) Do not change dates/company names or add new roles.

Original Resume (source of truth):
$cv_text
$jd_section

Output: A polished, ATS-optimized resume that aligns with the job description, highlights measurable impact, sounds human and professional, and positions the candidate as a top-tier applicant.
PROMPT;
}

/**
 * Rule-based revamp (fallback when AI is unavailable)
 */
function ruleBasedRevamp($cv_text, $target_job, $job_description) {
    // Extract key sections
    $sections = extractCVSections($cv_text);
    
    // Build revamped content
    $output = "RESUME - " . strtoupper($target_job) . "\n\n";
    
    // Professional summary
    $output .= "PROFESSIONAL SUMMARY\n";
    $output .= "Experienced professional seeking a position as $target_job. ";
    $output .= "Proven track record in delivering results through effective collaboration and problem-solving.\n\n";
    
    // Experience
    if (!empty($sections['experience'])) {
        $output .= "PROFESSIONAL EXPERIENCE\n";
        $output .= $sections['experience'] . "\n\n";
    }
    
    // Education
    if (!empty($sections['education'])) {
        $output .= "EDUCATION\n";
        $output .= $sections['education'] . "\n\n";
    }
    
    // Skills
    if (!empty($sections['skills'])) {
        $output .= "CORE COMPETENCIES\n";
        $output .= $sections['skills'] . "\n\n";
    }
    
    return $output;
}

/**
 * Extract CV sections using basic pattern matching
 */
function extractCVSections($text) {
    $sections = [
        'experience' => '',
        'education' => '',
        'skills' => ''
    ];
    
    // Basic section extraction (enhance with better parsing)
    if (preg_match('/experience(.+?)(?=education|skills|$)/is', $text, $matches)) {
        $sections['experience'] = trim($matches[1]);
    }
    
    if (preg_match('/education(.+?)(?=experience|skills|$)/is', $text, $matches)) {
        $sections['education'] = trim($matches[1]);
    }
    
    if (preg_match('/skills(.+?)(?=experience|education|$)/is', $text, $matches)) {
        $sections['skills'] = trim($matches[1]);
    }
    
    return $sections;
}

/**
 * Create PDF from revamped CV using TCPDF
 */
function createPDF($content, $output_path, $title, $contact_email = '') {
    // Check if TCPDF is available
    if (!class_exists('TCPDF')) {
        // Fallback to simple text file if TCPDF not installed
        return file_put_contents($output_path, $content) !== false;
    }
    
    try {
        // Create new PDF document
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Chronus Career Services');
        $pdf->SetAuthor('Chronus Solutions');
        $pdf->SetTitle('Revamped CV');
        $pdf->SetSubject('Professional Resume');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(true, 20);
        
        // Add a page
        $pdf->AddPage();
        
        // Convert plain text CV to formatted HTML
        $html = formatCVtoHTML($content, $contact_email);
        
        // Write content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Output PDF to file
        $pdf->Output($output_path, 'F');
        
        return file_exists($output_path);
    } catch (Exception $e) {
        error_log('PDF generation error: ' . $e->getMessage());
        // Fallback to text file
        return file_put_contents($output_path, $content) !== false;
    }
}

/**
 * Format CV text content into professional HTML for PDF
 */
function formatCVtoHTML($content, $contact_email = '') {
    // Clean content one more time before formatting
    $content = finalCleanContent($content);
    
    // Parse the content into sections
    $lines = explode("\n", $content);
    $html = '<style>
        body { font-family: helvetica, sans-serif; color: #333; }
        h1 { 
            font-size: 22pt; 
            color: #1a1a1a; 
            font-weight: bold; 
            margin: 0 0 5px 0; 
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .contact { 
            font-size: 10pt; 
            color: #666; 
            margin: 0 0 20px 0; 
            padding: 0 0 10px 0;
            border-bottom: 2px solid #2c3e50;
        }
        h2 { 
            font-size: 13pt; 
            color: #2c3e50; 
            font-weight: bold;
            margin: 15px 0 8px 0; 
            padding: 5px 0 3px 0;
            border-bottom: 2px solid #95cf4a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h3 { 
            font-size: 11pt; 
            color: #444; 
            font-weight: bold;
            margin: 10px 0 5px 0; 
        }
        p { 
            font-size: 10pt; 
            line-height: 1.5; 
            margin: 0 0 8px 0;
            text-align: justify;
        }
        ul { 
            margin: 5px 0 10px 15px; 
            padding: 0;
            list-style-type: disc;
        }
        li { 
            font-size: 10pt;
            line-height: 1.4;
            margin-bottom: 4px; 
        }
        .section-content {
            margin-bottom: 10px;
        }
    </style>';
    
    $current_section = '';
    $section_content = '';
    $name_found = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip empty lines and PDF artifacts
        if (empty($line) || isPDFArtifact($line)) continue;
        
        // Skip lines that look like PDF code
        if (strlen($line) < 3 || preg_match('/^[\d\s]+$/', $line)) continue;
        
        // Detect name (first meaningful line, usually all caps or title case)
        if (!$name_found && strlen($line) > 3 && !preg_match('/^(RESUME|CV|CURRICULUM)/i', $line)) {
            $html .= '<h1>' . htmlspecialchars($line) . '</h1>';
            if (!empty($contact_email)) {
                $html .= '<div class="contact">' . htmlspecialchars($contact_email) . '</div>';
            }
            $name_found = true;
            continue;
        }
        
        // Detect section headers (all caps, common CV sections)
        if (preg_match('/^(PROFESSIONAL SUMMARY|SUMMARY|EXPERIENCE|PROFESSIONAL EXPERIENCE|EDUCATION|SKILLS|CORE COMPETENCIES|CERTIFICATIONS|ACHIEVEMENTS|PROJECTS)$/i', $line)) {
            // Close previous section
            if (!empty($section_content)) {
                $html .= '<div class="section-content">' . $section_content . '</div>';
                $section_content = '';
            }
            // Start new section
            $html .= '<h2>' . htmlspecialchars($line) . '</h2>';
            $current_section = $line;
            continue;
        }
        
        // Detect job titles or subsection headers (contains dates, or bold-worthy)
        if (preg_match('/\d{4}|\b(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\b|Present/i', $line) && 
            strlen($line) < 100) {
            $section_content .= '<h3>' . htmlspecialchars($line) . '</h3>';
            continue;
        }
        
        // Detect bullet points
        if (preg_match('/^[-•*·]\s*/', $line)) {
            $line = preg_replace('/^[-•*·]\s*/', '', $line);
            $section_content .= '<ul><li>' . htmlspecialchars($line) . '</li></ul>';
            continue;
        }
        
        // Regular paragraph
        $section_content .= '<p>' . htmlspecialchars($line) . '</p>';
    }
    
    // Add last section
    if (!empty($section_content)) {
        $html .= '<div class="section-content">' . $section_content . '</div>';
    }
    
    return $html;
}

/**
 * Check if a line is a PDF artifact that should be filtered out
 */
function isPDFArtifact($line) {
    // Check for common PDF internal markers
    $pdf_patterns = [
        '/^endobj$/i',
        '/^\d+\s+\d+\s+obj$/i',
        '/^stream$/i',
        '/^endstream$/i',
        '/^<</',
        '/>>$/',
        '/^\/[A-Z]/i', // PDF operators like /Filter, /Type, etc.
        '/^\d+\s+\d+\s+R$/', // References like "55 0 R"
        '/Parent\s+\d+\s+\d+\s+R/i',
        '/\/Dest\[/i',
        '/\/Title\(/i',
        '/\/Count\s+-?\d+/i',
        '/\/First\s+\d+/i',
        '/\/Last\s+\d+/i',
        '/\/Next\s+\d+/i',
        '/\/Prev\s+\d+/i',
        '/\/XYZ\s+\d+/i',
        '/FlateDecode/i',
        '/Length\s+\d+/i'
    ];
    
    foreach ($pdf_patterns as $pattern) {
        if (preg_match($pattern, $line)) {
            return true;
        }
    }
    
    // Check if line is mostly non-alphanumeric (binary garbage)
    $alphanumeric = preg_replace('/[^a-zA-Z0-9]/', '', $line);
    if (strlen($line) > 10 && strlen($alphanumeric) < strlen($line) * 0.3) {
        return true;
    }
    
    return false;
}

/**
 * Final cleaning pass on content before PDF generation
 */
function finalCleanContent($content) {
    $lines = explode("\n", $content);
    $cleaned = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip empty lines
        if (empty($line)) continue;
        
        // Skip PDF artifacts
        if (isPDFArtifact($line)) continue;
        
        // Skip lines that are just numbers or single chars
        if (strlen($line) < 3) continue;
        
        // Skip lines with suspicious patterns
        if (preg_match('/^[\d\s]+$/', $line)) continue;
        if (preg_match('/^[^a-zA-Z]+$/', $line) && strlen($line) < 20) continue;
        
        $cleaned[] = $line;
    }
    
    return implode("\n", $cleaned);
}

/**
 * Send email with revamped CV using PHPMailer
 */
function sendRevampedCVEmail($email, $cv_path, $target_job) {
    // Check if email is enabled
    $email_enabled = defined('EMAIL_ENABLED') ? EMAIL_ENABLED : false;
    if (!$email_enabled) {
        return true; // Skip email if not configured
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $mail->Password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $mail->SMTPSecure = defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'tls';
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        
        // Recipients
        $from_email = defined('FROM_EMAIL') ? FROM_EMAIL : 'noreply@chronus.com';
        $from_name = defined('FROM_NAME') ? FROM_NAME : 'Chronus Career Services';
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($email);
        
        // Attachment
        if (file_exists($cv_path)) {
            $mail->addAttachment($cv_path, 'Revamped_CV.pdf');
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your Revamped CV for $target_job Position";
        $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #9acd32;'>Your CV Has Been Revamped!</h2>
                    <p>Great news! Your CV has been professionally revamped and optimized for <strong>$target_job</strong> positions.</p>
                    <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #9acd32; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #2c3e50;'>What We've Enhanced:</h3>
                        <ul>
                            <li>ATS-optimized formatting</li>
                            <li>Achievement-focused bullet points</li>
                            <li>Transferable skills highlighted</li>
                            <li>Industry-specific keywords</li>
                        </ul>
                    </div>
                    <p>Your revamped CV is attached to this email. Download it and start applying with confidence!</p>
                    <p style='margin-top: 30px;'>Best regards,<br><strong>Chronus Career Services</strong></p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>This is an automated message. Please do not reply directly to this email.</p>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Your CV has been revamped and optimized for $target_job positions. Please find your revamped CV attached.\n\nBest regards,\nChronus Career Services";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $mail->ErrorInfo);
        return false; // Don't fail the whole process if email fails
    }
}
