# Career Tools - CV Revamp & SOP Generator

## Overview

This module provides AI-powered CV revamp and Statement of Purpose (SOP) generation tools with strict anti-hallucination constraints to ensure authenticity and ATS compatibility.

## Features

### 1. CV Revamp Tool

- **Input**: Current CV (PDF/DOC/DOCX), target job title, job description (optional), email
- **Output**: ATS-optimized, humanized CV tailored to the target role
- **Processing**: `process_cv_revamp.php`

### 2. SOP Generator

- **Input**: CV file, program/position name, institution/company, career goals, motivation, email
- **Output**: Authentic, personalized Statement of Purpose
- **Processing**: `process_sop_generation.php`

## Critical AI Constraints

### Non-Negotiable Rules

Both tools implement strict AI prompting to prevent hallucination:

1. **FACT-BASED ONLY**: Use ONLY information from the provided CV and user inputs
2. **NO FABRICATION**: Never add experiences, skills, or achievements not present in the original
3. **TRANSFERABLE SKILLS**: Extract and highlight transferable skills from actual experiences
4. **HUMANIZED OUTPUT**: Maintain authentic, natural voice - avoid robotic templates
5. **ATS COMPATIBILITY**: Optimize for Applicant Tracking Systems with proper formatting and keywords
6. **ACHIEVEMENT-ORIENTED**: Reformat existing content using STAR method (Situation, Task, Action, Result)
7. **AUTHENTICITY**: If experience is limited, work with what exists - never embellish

## AI Configuration

### OpenAI Integration (Current Implementation)

The system uses OpenAI GPT-4 by default. Set your API key via environment variable:

```bash
# Windows
set OPENAI_API_KEY=your-api-key-here

# Linux/Mac
export OPENAI_API_KEY=your-api-key-here
```

Or configure directly in the PHP files (not recommended for production):

- `process_cv_revamp.php` line ~183
- `process_sop_generation.php` line ~139

### Fallback: Rule-Based Processing

If no API key is configured, the system automatically falls back to rule-based processing:

- **CV Revamp**: Uses `ruleBasedRevamp()` function
- **SOP Generation**: Uses `ruleBasedSOP()` function

These provide basic formatting without AI but maintain the no-hallucination principle.

### Alternative AI Providers

To use a different AI provider (Claude, Gemini, local LLMs, etc.), modify these functions:

**For CV Revamp** (`process_cv_revamp.php`):

```php
function generateRevampedCV($cv_text, $target_job, $job_description) {
    // Replace OpenAI API call with your provider
    // Ensure you maintain the strict prompt structure
}
```

**For SOP Generation** (`process_sop_generation.php`):

```php
function generateSOP($cv_text, $program_name, $institution_name, $career_goals, $motivation) {
    // Replace OpenAI API call with your provider
    // Ensure you maintain the strict prompt structure
}
```

## Prompt Engineering

### CV Revamp Prompt Structure

The `buildRevampPrompt()` function enforces:

- Temperature: 0.3 (lower = more factual, less creative)
- Max tokens: 2000
- System message: "You are an expert resume writer... You MUST ONLY use facts from the provided resume..."
- User prompt includes:
  - Original resume text
  - Target job description (if provided)
  - Explicit rules against fabrication
  - Output format requirements

### SOP Prompt Structure

The `buildSOPPrompt()` function enforces:

- Temperature: 0.4 (balanced for narrative flow while staying factual)
- Max tokens: 1500
- System message: "You MUST create authentic, personalized SOPs using ONLY the facts provided..."
- User prompt includes:
  - CV text
  - Career goals (user input)
  - Motivation (user input)
  - Program/institution details
  - Word count target (600-800 words)

## File Processing

### Supported Formats

- PDF (`.pdf`)
- Microsoft Word 2007+ (`.docx`)
- Microsoft Word 97-2003 (`.doc`)

### Text Extraction

**PDF Extraction**:

1. Primary: Smalot\PdfParser (composer package)
2. Fallback: `pdftotext` shell command
3. Last resort: Raw file content with basic cleanup

**DOCX Extraction**:

- Uses PHP's ZipArchive to extract `word/document.xml`
- Parses XML and strips tags

**DOC Extraction**:

- Uses `antiword` shell command
- Fallback: Raw file content

### Dependencies

**Optional but Recommended**:

```bash
# Install PDF parser via Composer
composer require smalot/pdfparser

# Install system tools (Ubuntu/Debian)
sudo apt-get install poppler-utils antiword

# Install system tools (Windows)
# Download Xpdf tools: https://www.xpdfreader.com/download.html
# Download Antiword: http://www.winfield.demon.nl/
```

## Data Storage

### Directory Structure

```
data/
├── cv_uploads/              # CV revamp uploads
│   ├── cv_xxxxx.pdf         # Uploaded CVs
│   ├── revamped_cv_xxxxx.pdf
│   └── metadata_xxxxx.json
│
└── sop_uploads/             # SOP generation uploads
    ├── sop_xxxxx.pdf        # Uploaded CVs
    ├── sop_xxxxx.pdf        # Generated SOPs
    └── metadata_xxxxx.json
```

### Security

- File size limit: 10MB per upload
- Allowed extensions validated server-side
- Files stored with unique IDs (uniqid())
- Original filenames preserved in metadata

## Email Delivery

Current implementation has placeholder email functions:

- `sendRevampedCVEmail()` in `process_cv_revamp.php`
- `sendSOPEmail()` in `process_sop_generation.php`

### Integrate with PHPMailer

The site already has PHPMailer installed. Update the email functions:

```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendRevampedCVEmail($email, $cv_path, $target_job) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-app-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('noreply@chronus.com', 'Chronus Career Services');
        $mail->addAddress($email);

        // Attachment
        $mail->addAttachment($cv_path, 'Revamped_CV.pdf');

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your Revamped CV for $target_job Position";
        $mail->Body = "<p>Your CV has been revamped and optimized for ATS compatibility.</p>
                       <p>Please find your revamped CV attached.</p>
                       <p>Best regards,<br>Chronus Career Services</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}
```

## PDF Generation

Current implementation uses basic file writing. For production, integrate a proper PDF library:

### Option 1: TCPDF

```bash
composer require tecnickcom/tcpdf
```

```php
require_once('vendor/autoload.php');

function createPDF($content, $output_path, $title) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Chronus Career Services');
    $pdf->SetTitle($title);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 5, $content, 0, 'L');

    $pdf->Output($output_path, 'F');
    return file_exists($output_path);
}
```

### Option 2: Dompdf (HTML to PDF)

```bash
composer require dompdf/dompdf
```

```php
use Dompdf\Dompdf;

function createPDF($content, $output_path, $title) {
    $dompdf = new Dompdf();

    // Wrap content in HTML
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>$title</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
            h1, h2, h3 { color: #2c3e50; }
        </style>
    </head>
    <body>
        " . nl2br(htmlspecialchars($content)) . "
    </body>
    </html>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    file_put_contents($output_path, $dompdf->output());
    return file_exists($output_path);
}
```

## Testing

### Manual Testing Checklist

**CV Revamp Tool**:

- [ ] Upload PDF CV successfully
- [ ] Upload DOCX CV successfully
- [ ] Required fields validation works
- [ ] File size limit enforced (10MB)
- [ ] Invalid file types rejected
- [ ] Text extraction from CV works
- [ ] AI generates factual output (no hallucination)
- [ ] Revamped CV downloads successfully
- [ ] Email sent to user

**SOP Generator**:

- [ ] Upload CV successfully
- [ ] All required fields validated
- [ ] Career goals captured correctly
- [ ] Motivation captured correctly
- [ ] SOP generated authentically
- [ ] No fabricated experiences
- [ ] SOP downloads successfully
- [ ] Email sent to user

### AI Output Quality Checks

For each generation, verify:

1. **No fabricated content**: All experiences/skills mentioned exist in original CV
2. **Transferable skills**: Skills are properly extracted and rephrased for target role
3. **Human tone**: Language sounds natural, not templated
4. **ATS keywords**: Relevant keywords from job description included
5. **Achievement focus**: Bullets reformatted to highlight results
6. **Structural integrity**: Proper sections and formatting

## Troubleshooting

### Common Issues

**"Could not extract text from CV"**

- Ensure PDF/DOCX is not encrypted
- Install pdftotext or antiword system tools
- Check file permissions on upload directory

**"Failed to generate revamped CV"**

- Verify OpenAI API key is set correctly
- Check API quota/billing
- Review error logs for API response codes
- Falls back to rule-based processing

**"Failed to create PDF"**

- Install TCPDF or Dompdf via Composer
- Check write permissions on data/cv_uploads and data/sop_uploads
- Verify disk space available

**AI output contains fabrications**

- Review prompt structure in `buildRevampPrompt()` / `buildSOPPrompt()`
- Lower temperature value (more factual, less creative)
- Add more explicit constraints to system message
- Consider using Claude (better at following constraints)

## Future Enhancements

1. **Real-time preview**: Show CV revamp preview before download
2. **Iterative refinement**: Allow users to request adjustments
3. **Multi-format output**: Export as PDF, DOCX, TXT
4. **Template selection**: Multiple CV templates (Modern, Traditional, ATS-focused)
5. **Skills extraction dashboard**: Show extracted skills with confidence scores
6. **Version history**: Track multiple revisions
7. **Batch processing**: Process multiple CVs at once
8. **Analytics**: Track conversion rates, popular fields, success metrics

## Support & Maintenance

- **Logs**: Check PHP error logs for debugging
- **API costs**: Monitor OpenAI API usage for cost management
- **Storage cleanup**: Implement cron job to delete old files (>30 days)
- **Backup**: Regular backups of data/ directory

## License & Credits

Part of Chronus Solutions career services platform.
AI integration follows OpenAI usage policies and ethical AI guidelines.
