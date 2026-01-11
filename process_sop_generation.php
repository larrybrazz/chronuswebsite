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

// Load Composer autoloader for dependencies first
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

function json_error($message) {
    ob_clean(); // Clear any output buffer
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Invalid request method');
}

// Validate required fields
if (empty($_FILES['cv_file']) || empty($_POST['program_name']) || empty($_POST['institution_name']) || empty($_POST['career_goals']) || empty($_POST['motivation']) || empty($_POST['email'])) {
    json_error('Please fill in all required fields');
}

// Validate email
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    json_error('Invalid email address');
}

// Validate file upload
if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) {
    $error_msg = 'File upload failed. ';
    if (isset($_FILES['cv_file']['error'])) {
        switch ($_FILES['cv_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error_msg .= 'File is too large (exceeds server limit).';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error_msg .= 'File exceeds 10MB size limit.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_msg .= 'No file was selected. Please upload a CV file.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_msg .= 'File upload was incomplete. Please try again.';
                break;
            default:
                $error_msg .= 'An error occurred during file upload.';
        }
    }
    json_error($error_msg);
}

$cv_file = $_FILES['cv_file'];
$allowed_extensions = ['pdf', 'doc', 'docx'];
$file_extension = strtolower(pathinfo($cv_file['name'], PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    json_error('Invalid file format. Please upload PDF, DOC, or DOCX');
}

if ($cv_file['size'] > 10 * 1024 * 1024) { // 10MB limit
    json_error('File size exceeds 10MB limit');
}

if ($cv_file['size'] === 0) {
    json_error('The uploaded file is empty. Please upload a valid CV file.');
}

// Create uploads directory if it doesn't exist
$upload_dir = defined('SOP_UPLOAD_DIR') ? SOP_UPLOAD_DIR : __DIR__ . '/data/sop_uploads';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        json_error('Failed to create upload directory. Please try again.');
    }
}

// Generate unique filename
$unique_id = uniqid('sop_', true);
$cv_filename = $unique_id . '.' . $file_extension;
$cv_path = $upload_dir . '/' . $cv_filename;

// Move uploaded file
if (!move_uploaded_file($cv_file['tmp_name'], $cv_path)) {
    json_error('Failed to upload file. Please check file permissions and try again.');
}

// Extract text from CV
$cv_text = extractTextFromCV($cv_path, $file_extension);

// Clean extracted text
$cv_text_cleaned = trim($cv_text);
if (empty($cv_text_cleaned) || strlen($cv_text_cleaned) < 50) {
    // Provide helpful error message
    $error_msg = 'Could not extract readable text from your CV. ';
    
    if ($file_extension === 'pdf') {
        $error_msg .= 'The PDF may be scanned/image-based or protected. Try: 1) Saving as DOCX, or 2) Using a PDF converter to extract text first.';
    } elseif ($file_extension === 'doc') {
        $error_msg .= 'Old DOC format has compatibility issues. Please save as DOCX and try again.';
    } else {
        $error_msg .= 'Please ensure the file contains readable text.';
    }
    
    // Clean up uploaded file
    @unlink($cv_path);
    json_error($error_msg);
}

// Get form data
$program_name = htmlspecialchars(trim($_POST['program_name']));
$institution_name = htmlspecialchars(trim($_POST['institution_name']));
$job_description = htmlspecialchars(trim($_POST['job_description']));
$core_values = htmlspecialchars(trim($_POST['core_values']));
$success_profile = htmlspecialchars(trim($_POST['success_profile']));
$career_goals = htmlspecialchars(trim($_POST['career_goals']));
$motivation = htmlspecialchars(trim($_POST['motivation']));

// Generate SOP using AI
$sop = generateSOP($cv_text, $program_name, $institution_name, $job_description, $core_values, $success_profile, $career_goals, $motivation);

if (!$sop) {
    json_error('Failed to generate Statement of Purpose. Please try again.');
}

// Save SOP as PDF
$output_filename = 'sop_' . $unique_id . '.pdf';
$output_path = $upload_dir . '/' . $output_filename;

// Generate PDF using TCPDF
$pdf_created = createPDF($sop, $output_path, $program_name, $email);

if (!$pdf_created) {
    json_error('Failed to create PDF. The SOP has been generated but could not be saved.');
}

// Save metadata
$metadata = [
    'id' => $unique_id,
    'email' => $email,
    'program_name' => $program_name,
    'institution_name' => $institution_name,
    'career_goals' => $career_goals,
    'motivation' => $motivation,
    'original_cv' => $cv_file['name'],
    'timestamp' => date('Y-m-d H:i:s'),
    'output_file' => $output_filename
];

file_put_contents($upload_dir . '/metadata_' . $unique_id . '.json', json_encode($metadata, JSON_PRETTY_PRINT));

// Send email with SOP
sendSOPEmail($email, $output_path, $program_name, $institution_name);

// Return success response
$message = class_exists('TCPDF')
    ? 'Your Statement of Purpose has been generated successfully! Check your email for the PDF download link.'
    : 'Your Statement of Purpose has been generated successfully! Download ready (Note: Install TCPDF for PDF format).';

ob_clean(); // Clear any output buffer before sending JSON
echo json_encode([
    'success' => true,
    'message' => $message,
    'download_url' => 'download_file.php?file=' . urlencode($output_filename) . '&type=sop',
    'file_type' => $file_extension
]);

/**
 * Extract text from CV file
 */
function extractTextFromCV($filepath, $extension) {
    $text = '';
    
    if ($extension === 'pdf') {
        // Basic PDF text extraction
        if (class_exists('Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filepath);
                $text = $pdf->getText();
            } catch (Exception $e) {
                $text = shell_exec("pdftotext " . escapeshellarg($filepath) . " -");
            }
        } else {
            $text = file_get_contents($filepath);
            $text = preg_replace('/[^\x20-\x7E\n]/', '', $text);
        }
    } elseif ($extension === 'docx') {
        $zip = new ZipArchive();
        if ($zip->open($filepath)) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();
            $xml_obj = simplexml_load_string($xml);
            $text = strip_tags($xml_obj->asXML());
        }
    } elseif ($extension === 'doc') {
        $text = shell_exec("antiword " . escapeshellarg($filepath));
        if (empty($text)) {
            $text = file_get_contents($filepath);
        }
    }
    
    return trim($text);
}

/**
 * Generate SOP using AI with strict constraints
 */
function generateSOP($cv_text, $program_name, $institution_name, $job_description, $core_values, $success_profile, $career_goals, $motivation) {
    // AI API configuration
    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : getenv('OPENAI_API_KEY');
    $use_ai = defined('ENABLE_AI_PROCESSING') ? ENABLE_AI_PROCESSING : true;
    
    if (!$use_ai || empty($api_key) || $api_key === 'your-api-key-here') {
        return ruleBasedSOP($cv_text, $program_name, $institution_name, $job_description, $core_values, $success_profile, $career_goals, $motivation);
    }
    
    // Build strict prompt
    $prompt = buildSOPPrompt($cv_text, $program_name, $institution_name, $job_description, $core_values, $success_profile, $career_goals, $motivation);
    
    // Call AI API
    $api_endpoint = defined('OPENAI_ENDPOINT') ? OPENAI_ENDPOINT : 'https://api.openai.com/v1/chat/completions';
    $model = defined('OPENAI_MODEL') ? OPENAI_MODEL : 'gpt-4';
    $temperature = defined('SOP_TEMPERATURE') ? SOP_TEMPERATURE : 0.4;
    $max_tokens = defined('SOP_MAX_TOKENS') ? SOP_MAX_TOKENS : 1500;
    
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
                    'content' => 'Act as a Human Resource Career Expert. Create a polished, human supporting statement that meets the job criteria, uses only factual achievements from the CV and user inputs, integrates ATS-relevant keywords, highlights measurable impact and leadership, and mirrors the organization’s tone for supporting statements. Avoid robotic or AI-like phrasing and never fabricate content.'
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
        return ruleBasedSOP($cv_text, $program_name, $institution_name, $job_description, $core_values, $success_profile, $career_goals, $motivation);
    }
    
    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? null;
}

/**
 * Build AI prompt for SOP generation with strict constraints
 */
function buildSOPPrompt($cv_text, $program_name, $institution_name, $job_description, $core_values, $success_profile, $career_goals, $motivation) {
    return <<<PROMPT
Act as a Human Resource Career Expert and craft a compelling supporting statement for a $program_name application/role at $institution_name.

Non-negotiable rules:
1) Meet the stated criteria for the position using the job description as the guide.
2) Be fact-based: use only real achievements/responsibilities from the CV and user inputs. No fabrication.
3) Sound human and natural; avoid robotic or AI-like phrasing to pass AI detection.
4) Include key ATS keywords/phrases relevant to the role and industry.
5) Position the candidate as a high-achieving corporate asset, emphasizing measurable impact and leadership.
6) Integrate specific examples with success metrics (cost savings, efficiency gains, team leadership, project delivery, revenue/quality improvements).
7) Follow the organization’s standard tone/format for supporting statements; be engaging, authentic, and professional.
8) Highlight transferable skills and future value to the company.

Applicant's CV (source of facts):
$cv_text

Job/Program Description (criteria/keywords):
$job_description

Organization Core Values (tone and proof points):
$core_values

Success Profile for this Role (competency focus):
$success_profile

Career Goals (candidate):
$career_goals

Motivation for this program/role (candidate):
$motivation

Target Program/Position: $program_name
Institution/Company: $institution_name

Output: A polished, humanized supporting statement that aligns with the job requirements, showcases strengths and achievements with metrics, and reads like it was written by the candidate (not AI).
PROMPT;
}

/**
 * Rule-based SOP generation (fallback)
 */
function ruleBasedSOP($cv_text, $program_name, $institution_name, $job_description, $core_values, $success_profile, $career_goals, $motivation) {
    // Extract detailed information from CV
    $skills = extractDetailedSkills($cv_text);
    $experience_bullets = extractDetailedExperience($cv_text);
    $achievements = extractAchievements($cv_text);
    $concrete_examples = extractConcreteExamples($cv_text);
    $core_values_list = array_filter(array_map('trim', preg_split('/[,;]/', $core_values)));
    
    $sop = "STATEMENT OF PURPOSE\n\n";
    $sop .= "Application for: $program_name at $institution_name\n\n";
    
    // ===== INTRODUCTION: ROLE & MOTIVATION =====
    $sop .= "INTRODUCTION\n\n";
    $sop .= "I am applying for the $program_name position at $institution_name to ";
    $sop .= strtolower($motivation[0]) . $motivation . "\n";
    $sop .= "With my background in " . implode(', ', array_slice($skills, 0, 2)) . ", ";
    $sop .= "I am confident I can make meaningful contributions to your team.\n\n";
    
    // ===== EVIDENCE OF SKILLS: CONCRETE EXAMPLES =====
    $sop .= "EVIDENCE OF SKILLS & ACHIEVEMENTS\n\n";
    
    if (!empty($concrete_examples)) {
        $sop .= "My experience demonstrates the capabilities required for this role:\n\n";
        foreach ($concrete_examples as $example) {
            $sop .= "• " . trim($example) . "\n";
        }
    } else if (!empty($experience_bullets)) {
        $sop .= "My professional experience includes:\n\n";
        foreach ($experience_bullets as $bullet) {
            $sop .= "• " . trim($bullet) . "\n";
        }
    }
    
    if (!empty($achievements)) {
        $sop .= "\nKey achievements demonstrating impact:\n\n";
        foreach ($achievements as $achievement) {
            $sop .= "• " . trim($achievement) . "\n";
        }
    }
    $sop .= "\n";
    
    // ===== ALIGNMENT WITH VALUES =====
    if (!empty($core_values_list)) {
        $sop .= "ALIGNMENT WITH ORGANIZATIONAL VALUES\n\n";
        $sop .= "I am committed to the values that define " . $institution_name . ":\n\n";
        
        foreach ($core_values_list as $idx => $value) {
            $value_clean = trim($value);
            $value_example = generateValueExample($value_clean, $cv_text);
            $sop .= "• " . ucfirst($value_clean) . ": " . $value_example . "\n";
        }
        $sop .= "\n";
    }
    
    // ===== ALIGNMENT WITH JOB REQUIREMENTS =====
    $sop .= "HOW I MEET THE PERSON SPECIFICATION\n\n";
    $requirement_examples = extractRequirementExamples($job_description, $cv_text);
    if (!empty($requirement_examples)) {
        foreach ($requirement_examples as $requirement => $example) {
            $sop .= "• " . $requirement . ": " . $example . "\n";
        }
    } else {
        $sop .= "My experience directly aligns with the key requirements:\n";
        $mapping = mapExperienceToRequirements($cv_text, $job_description);
        foreach ($mapping as $requirement => $description) {
            $sop .= "• " . ucfirst($requirement) . ": " . $description . "\n";
        }
    }
    $sop .= "\n";
    
    // ===== CAREER GOALS & PROFESSIONAL DEVELOPMENT =====
    $sop .= "CAREER GOALS & PROFESSIONAL DEVELOPMENT\n\n";
    $sop .= "This opportunity is a strategic step in my professional journey. " . 
            trim($career_goals) . " ";
    $sop .= "The " . $program_name . " role at " . $institution_name . " will enable me to develop these skills ";
    $sop .= "while contributing to meaningful work.\n\n";
    
    // ===== CONCLUSION: ENTHUSIASM & READINESS =====
    $sop .= "CONCLUSION\n\n";
    $sop .= "I am genuinely enthusiastic about this opportunity and ready to bring my ";
    $sop .= "expertise, dedication, and values to the " . $program_name . " team at " . $institution_name . ". ";
    $sop .= "I am committed to delivering excellence and contributing to your organization's success. ";
    $sop .= "Thank you for considering my application. I look forward to discussing how I can add value to your team.\n";
    
    return $sop;
}

/**
 * Extract concrete examples with context and impact
 */
function extractConcreteExamples($text) {
    $lines = explode("\n", $text);
    $examples = [];
    
    // Look for sentences/bullets that contain:
    // 1. Action verb + specific activity + measurable result
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (strlen($line) > 50 && strlen($line) < 300) {
            // Pattern: action verb + activity + result/impact
            if (preg_match('/\b(managed|led|developed|created|implemented|achieved|delivered|coordinated|improved|increased|reduced|designed|optimized)\b.{10,}(by|resulting in|leading to|with|to achieve|\d+%|\d+\s*\w+)/i', $line)) {
                $line = preg_replace('/^\s*[-•]\s*/', '', $line);
                $line = trim($line);
                if (strlen($line) > 20) {
                    $examples[] = $line;
                }
            }
        }
    }
    
    return array_unique(array_slice($examples, 0, 4));
}

/**
 * Extract requirement examples from job description and CV
 */
function extractRequirementExamples($job_description, $cv_text) {
    $examples = [];
    
    // Common NHS/professional role requirements
    $requirements = [
        'Patient/Customer Focus' => 'demonstrated through',
        'Communication' => 'in presentations, reports, or stakeholder engagement',
        'Leadership' => 'managed teams, delegated work, or mentored colleagues',
        'Clinical Knowledge' => 'in relevant medical, health, or technical areas',
        'Teamwork' => 'collaborated across departments or with diverse teams',
        'Problem-solving' => 'identified solutions or resolved complex issues',
        'Time Management' => 'handled multiple priorities, met deadlines',
        'Quality Focus' => 'ensured accuracy, standards, or process improvements'
    ];
    
    $job_lower = strtolower($job_description);
    $cv_lower = strtolower($cv_text);
    
    foreach ($requirements as $requirement => $pattern) {
        $req_keyword = strtok(strtolower($requirement), ' ');
        
        if (strpos($job_lower, $req_keyword) !== false && strpos($cv_lower, $req_keyword) !== false) {
            $example = generateRequirementEvidence($requirement, $cv_text);
            if (!empty($example)) {
                $examples[$requirement] = $example;
            }
        }
    }
    
    return $examples;
}

/**
 * Generate specific evidence for a requirement based on CV
 */
function generateRequirementEvidence($requirement, $cv_text) {
    $cv_lower = strtolower($cv_text);
    
    $evidence_map = [
        'Patient/Customer Focus' => [
            'patient' => 'prioritized patient satisfaction and care outcomes in all interactions',
            'customer' => 'consistently ensured customer satisfaction through quality service delivery',
            'stakeholder' => 'engaged with stakeholders to understand and meet their needs'
        ],
        'Communication' => [
            'presentation' => 'delivered presentations and reports to diverse audiences',
            'communication' => 'communicated complex information clearly to various stakeholders',
            'liaison' => 'served as liaison between teams, translating requirements effectively'
        ],
        'Leadership' => [
            'manage' => 'managed teams and delegated tasks effectively',
            'lead' => 'led initiatives and mentored junior colleagues',
            'supervise' => 'supervised staff and ensured accountability'
        ],
        'Clinical Knowledge' => [
            'clinical' => 'applied clinical knowledge and professional standards in practice',
            'medical' => 'demonstrated expertise in relevant medical/health areas',
            'technical' => 'applied technical knowledge to solve workplace challenges'
        ],
        'Teamwork' => [
            'team' => 'contributed to team success through active collaboration',
            'collaborated' => 'worked cross-functionally with diverse teams',
            'cooperative' => 'fostered positive working relationships with colleagues'
        ],
        'Problem-solving' => [
            'identify' => 'identified and resolved complex workplace problems',
            'solve' => 'developed solutions to improve processes and outcomes',
            'troubleshoot' => 'troubleshot issues and implemented improvements'
        ],
        'Time Management' => [
            'deadline' => 'managed multiple priorities and consistently met deadlines',
            'schedule' => 'organized complex schedules and competing demands',
            'priorit' => 'prioritized workload to ensure timely delivery'
        ],
        'Quality Focus' => [
            'quality' => 'maintained high quality standards in all work',
            'audit' => 'participated in quality audits and compliance reviews',
            'improve' => 'implemented process improvements to enhance outcomes'
        ]
    ];
    
    if (isset($evidence_map[$requirement])) {
        foreach ($evidence_map[$requirement] as $keyword => $evidence) {
            if (strpos($cv_lower, $keyword) !== false) {
                return $evidence;
            }
        }
    }
    
    return ucfirst($requirement) . ' demonstrated through professional experience and development';
}

/**
 * Extract detailed skills from CV with technical and soft skills
 */
function extractDetailedSkills($text) {
    // Expanded skill keywords with variations
    $skill_patterns = [
        'data analysis' => ['data analysis', 'analytics', 'statistical'],
        'project management' => ['project management', 'agile', 'scrum', 'stakeholder management'],
        'leadership' => ['leadership', 'team lead', 'management', 'director'],
        'communication' => ['communication', 'presentation', 'stakeholder engagement'],
        'technical' => ['technical', 'programming', 'software development', 'database', 'sql', 'python', 'java', 'javascript'],
        'business analysis' => ['business analysis', 'requirements gathering', 'business process'],
        'problem-solving' => ['problem solving', 'troubleshooting', 'analysis'],
        'strategic planning' => ['strategic planning', 'strategic', 'planning', 'roadmap'],
        'collaboration' => ['collaboration', 'teamwork', 'cross-functional'],
        'research' => ['research', 'investigation', 'analysis']
    ];
    
    $found_skills = [];
    $text_lower = strtolower($text);
    
    foreach ($skill_patterns as $main_skill => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($text_lower, $keyword) !== false) {
                $found_skills[$main_skill] = true;
                break;
            }
        }
    }
    
    return array_keys($found_skills);
}

/**
 * Extract detailed experience bullets from CV
 */
function extractDetailedExperience($text) {
    $lines = explode("\n", $text);
    $experience = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Look for action verbs and quantifiable achievements
        if (strlen($line) > 40 && strlen($line) < 250) {
            $action_verbs = '/\b(managed|led|developed|created|implemented|achieved|delivered|coordinated|improved|increased|reduced|designed|optimized|established|expanded)\b/i';
            if (preg_match($action_verbs, $line)) {
                // Clean up the line
                $line = preg_replace('/^\s*[-•]\s*/', '', $line);
                $line = trim($line);
                if (!empty($line) && strlen($line) > 20) {
                    $experience[] = $line;
                }
            }
        }
    }
    
    // Return unique, up to 5 strongest experiences
    return array_unique(array_slice($experience, 0, 5));
}

/**
 * Extract achievements with metrics and numbers
 */
function extractAchievements($text) {
    $lines = explode("\n", $text);
    $achievements = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Look for lines with numbers/percentages indicating results
        if (strlen($line) > 40 && strlen($line) < 250) {
            if (preg_match('/\b(\d+%|\d+\s*(million|thousand|hundred|hours|days|customers|users|projects|initiatives))\b/i', $line)) {
                $line = preg_replace('/^\s*[-•]\s*/', '', $line);
                $line = trim($line);
                if (!empty($line)) {
                    $achievements[] = $line;
                }
            }
        }
    }
    
    return array_unique(array_slice($achievements, 0, 4));
}

/**
 * Map CV experience to job requirements
 */
function mapExperienceToRequirements($cv_text, $job_description) {
    $mapping = [];
    
    // Extract key requirements from job description
    $job_lower = strtolower($job_description);
    $cv_lower = strtolower($cv_text);
    
    // Common requirement keywords
    $requirements = [
        'leadership' => 'Led teams and initiatives, demonstrating ability to guide and motivate others',
        'communication' => 'Developed strong communication skills through stakeholder engagement and team collaboration',
        'technical skills' => 'Proficient in relevant technical tools and platforms',
        'analytical skills' => 'Demonstrated ability to analyze complex data and information for decision-making',
        'project management' => 'Successfully managed multiple projects with attention to deadlines and quality',
        'problem-solving' => 'Identified and resolved complex business challenges',
        'business acumen' => 'Developed understanding of business operations and strategic objectives',
        'customer focus' => 'Consistently prioritized customer needs and satisfaction'
    ];
    
    foreach ($requirements as $requirement => $description) {
        if (strpos($job_lower, $requirement) !== false && strpos($cv_lower, strtok($requirement, ' ')) !== false) {
            $mapping[$requirement] = $description;
        }
    }
    
    // Limit to 4 most relevant mappings
    return array_slice($mapping, 0, 4);
}

/**
 * Generate specific example for core value alignment
 */
function generateValueExample($value, $cv_text) {
    $value_lower = strtolower($value);
    $cv_lower = strtolower($cv_text);
    
    // Value-specific examples
    $value_examples = [
        'integrity' => 'maintained honest communication and ethical standards in all professional interactions',
        'innovation' => 'introduced new approaches and solutions to improve processes and outcomes',
        'excellence' => 'consistently delivered high-quality work and exceeded expectations',
        'collaboration' => 'worked effectively with diverse teams to achieve shared objectives',
        'accountability' => 'took responsibility for projects and delivered results on schedule',
        'leadership' => 'guided teams and provided mentorship to colleagues',
        'customer-focused' => 'prioritized customer satisfaction and needs in all initiatives',
        'continuous learning' => 'pursued professional development and stayed current with industry trends',
        'transparency' => 'communicated openly and honestly with all stakeholders',
        'teamwork' => 'contributed to team success through active participation and support'
    ];
    
    foreach ($value_examples as $key => $example) {
        if (strpos($value_lower, $key) !== false || strpos($cv_lower, $key) !== false) {
            return $example;
        }
    }
    
    return 'brought these values to my work through professional achievement and team collaboration';
}

/**
 * Extract success profile items
 */
function extractSuccessProfileItems($success_profile) {
    // Split by common delimiters and clean up
    $items = preg_split('/[,;•\n-]/', $success_profile);
    $cleaned = [];
    
    foreach ($items as $item) {
        $item = trim($item);
        // Filter out very short items and empty ones
        if (strlen($item) > 5 && strlen($item) < 200) {
            $cleaned[] = $item;
        }
    }
    
    return array_unique(array_slice($cleaned, 0, 6));
}

/**
 * Create PDF from SOP using TCPDF
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
        $pdf->SetTitle('Statement of Purpose');
        $pdf->SetSubject('Academic Application');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins (academic style - wider margins)
        $pdf->SetMargins(25, 25, 25);
        $pdf->SetAutoPageBreak(true, 25);
        
        // Add a page
        $pdf->AddPage();
        
        // Convert plain text SOP to formatted HTML
        $html = formatSOPtoHTML($content, $title, $contact_email);
        
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
 * Format SOP text content into professional HTML for PDF
 */
function formatSOPtoHTML($content, $title, $contact_email = '') {
    $html = '<style>
        body { font-family: times, serif; color: #000; }
        h1 { 
            font-size: 16pt; 
            text-align: center; 
            color: #1a1a1a;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle { 
            font-size: 12pt; 
            text-align: center; 
            color: #444;
            margin: 0 0 5px 0;
            font-weight: normal;
        }
        .contact { 
            font-size: 10pt; 
            text-align: center;
            color: #666;
            margin: 0 0 30px 0;
        }
        h2 { 
            font-size: 13pt; 
            color: #2c3e50;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        p { 
            font-size: 11pt; 
            line-height: 1.8; 
            text-align: justify;
            text-indent: 20px;
            margin: 0 0 12px 0;
        }
        p.no-indent {
            text-indent: 0;
        }
    </style>';
    
    // Title
    $html .= '<h1>STATEMENT OF PURPOSE</h1>';
    if (!empty($title)) {
        $html .= '<div class="subtitle">' . htmlspecialchars($title) . '</div>';
    }
    if (!empty($contact_email)) {
        $html .= '<div class="contact">' . htmlspecialchars($contact_email) . '</div>';
    }
    
    // Parse content into paragraphs and sections
    $lines = explode("\n", $content);
    $current_paragraph = '';
    $is_first_para_in_section = true;
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            // Empty line - end current paragraph
            if (!empty($current_paragraph)) {
                $indent_class = $is_first_para_in_section ? ' class="no-indent"' : '';
                $html .= '<p' . $indent_class . '>' . htmlspecialchars($current_paragraph) . '</p>';
                $current_paragraph = '';
                $is_first_para_in_section = false;
            }
            continue;
        }
        
        // Check if line is a section header (all caps, short)
        if (preg_match('/^[A-Z\s]{5,50}$/', $line) && strlen($line) < 50) {
            // Close previous paragraph
            if (!empty($current_paragraph)) {
                $html .= '<p>' . htmlspecialchars($current_paragraph) . '</p>';
                $current_paragraph = '';
            }
            // Add section header
            $html .= '<h2>' . htmlspecialchars($line) . '</h2>';
            $is_first_para_in_section = true;
            continue;
        }
        
        // Add line to current paragraph
        if (!empty($current_paragraph)) {
            $current_paragraph .= ' ';
        }
        $current_paragraph .= $line;
    }
    
    // Add last paragraph
    if (!empty($current_paragraph)) {
        $html .= '<p>' . htmlspecialchars($current_paragraph) . '</p>';
    }
    
    return $html;
}

/**
 * Send email with SOP using PHPMailer
 */
function sendSOPEmail($email, $sop_path, $program_name, $institution_name) {
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
        if (file_exists($sop_path)) {
            $mail->addAttachment($sop_path, 'Statement_of_Purpose.pdf');
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your Statement of Purpose for $program_name at $institution_name";
        $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #28a745;'>Your Statement of Purpose is Ready!</h2>
                    <p>Congratulations! Your Statement of Purpose has been professionally crafted for your <strong>$program_name</strong> application at <strong>$institution_name</strong>.</p>
                    <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #2c3e50;'>What Makes Your SOP Stand Out:</h3>
                        <ul>
                            <li>Authentic narrative based on your real experiences</li>
                            <li>Clear connection between past achievements and future goals</li>
                            <li>Transferable skills highlighted effectively</li>
                            <li>Professional structure and tone</li>
                        </ul>
                    </div>
                    <p>Your Statement of Purpose is attached to this email. Review it carefully and feel free to personalize it further before submission.</p>
                    <p><strong>Next Steps:</strong></p>
                    <ul>
                        <li>Read through your SOP completely</li>
                        <li>Verify all information is accurate</li>
                        <li>Add any personal touches that reflect your unique voice</li>
                        <li>Have it reviewed by a mentor or advisor</li>
                    </ul>
                    <p style='margin-top: 30px;'>Wishing you the best with your application!<br><strong>Chronus Career Services</strong></p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>This is an automated message. Please do not reply directly to this email.</p>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Your Statement of Purpose for $program_name at $institution_name has been generated.\n\nPlease find your SOP attached.\n\nBest regards,\nChronus Career Services";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $mail->ErrorInfo);
        return false; // Don't fail the whole process if email fails
    }
}
