# Quick Setup Guide - Career Tools

## Step 1: Verify File Structure

Ensure these files exist:

- `cv-tools.php` - Main career tools page
- `process_cv_revamp.php` - CV revamp backend
- `process_sop_generation.php` - SOP generation backend
- `career-assessment.php` - Updated with "Revamp Your CV" button
- `inc/header.php` - Updated navbar (Trainings → Career Tools)
- `index.php` - Added trainings section to homepage

## Step 2: Create Required Directories

Run in terminal (from project root):

```bash
# Windows (PowerShell)
New-Item -Path "data\cv_uploads" -ItemType Directory -Force
New-Item -Path "data\sop_uploads" -ItemType Directory -Force

# Linux/Mac
mkdir -p data/cv_uploads
mkdir -p data/sop_uploads
```

Or create manually:

- `data/cv_uploads/`
- `data/sop_uploads/`

## Step 3: Set Permissions (Linux/Mac only)

```bash
chmod 755 data/cv_uploads
chmod 755 data/sop_uploads
```

## Step 4: Configure AI API

### Option A: Use OpenAI (Recommended)

1. Get API key from https://platform.openai.com/api-keys
2. Set environment variable:

**Windows:**

```cmd
set OPENAI_API_KEY=sk-your-api-key-here
```

**Linux/Mac:**

```bash
export OPENAI_API_KEY=sk-your-api-key-here
```

Or edit the files directly (line ~183 in `process_cv_revamp.php` and ~139 in `process_sop_generation.php`):

```php
$api_key = 'sk-your-actual-api-key-here';
```

### Option B: Use Fallback (No AI)

If you don't set an API key, the system automatically uses rule-based processing:

- Basic text reformatting
- Section extraction
- No advanced AI features
- Still maintains no-hallucination principle

## Step 5: Install Optional Dependencies

These are optional but improve functionality:

### PHP Composer Packages

```bash
# PDF parsing (highly recommended)
composer require smalot/pdfparser

# Better PDF generation (choose one)
composer require tecnickcom/tcpdf
# OR
composer require dompdf/dompdf
```

### System Tools (for better file parsing)

**Ubuntu/Debian:**

```bash
sudo apt-get install poppler-utils antiword
```

**Windows:**

- Download Xpdf tools: https://www.xpdfreader.com/download.html
- Download Antiword: http://www.winfield.demon.nl/

**Mac:**

```bash
brew install poppler antiword
```

## Step 6: Test the Setup

1. Navigate to `http://localhost/chronuswebsite-main/cv-tools.php`
2. Try uploading a sample CV (PDF or DOCX)
3. Fill in required fields
4. Click "Generate"
5. Check for errors in browser console and PHP logs

## Step 7: Configure Email (Optional)

Edit `process_cv_revamp.php` and `process_sop_generation.php`:

Find the `sendRevampedCVEmail()` and `sendSOPEmail()` functions and integrate PHPMailer:

```php
use PHPMailer\PHPMailer\PHPMailer;
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

function sendRevampedCVEmail($email, $cv_path, $target_job) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com';
    $mail->Password = 'your-app-password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('noreply@chronus.com', 'Chronus Career Services');
    $mail->addAddress($email);
    $mail->addAttachment($cv_path);

    $mail->Subject = "Your Revamped CV for $target_job";
    $mail->Body = "Your CV has been revamped...";

    return $mail->send();
}
```

## Verification Checklist

- [ ] Directories created: `data/cv_uploads/`, `data/sop_uploads/`
- [ ] Permissions set (755 on Linux/Mac)
- [ ] API key configured (if using AI) OR fallback mode accepted
- [ ] Can access `cv-tools.php` in browser
- [ ] Navbar shows "Career Tools" instead of "Trainings"
- [ ] Homepage shows trainings section
- [ ] Career assessment success modal has "Revamp Your CV" button
- [ ] File upload works (test with sample PDF)
- [ ] No fatal PHP errors

## Common First-Time Issues

**Issue: "Failed to upload file"**

- Check directory permissions
- Verify directories exist
- Check disk space

**Issue: "Could not extract text from CV"**

- Install pdftotext/antiword
- Try a different CV file
- Check if PDF is encrypted/password protected

**Issue: "Failed to generate revamped CV"**

- Verify API key is correct
- Check API quota/billing
- Falls back to rule-based if API unavailable

**Issue: White screen / 500 error**

- Check PHP error logs: `xampp/php/logs/` or `/var/log/apache2/error.log`
- Enable error display: `ini_set('display_errors', 1);`
- Verify all PHP files are valid syntax

## Next Steps

1. **Test with real CV**: Upload your own CV and verify output quality
2. **Customize prompts**: Adjust AI prompts in `buildRevampPrompt()` and `buildSOPPrompt()` for better results
3. **Configure email**: Set up SMTP for email delivery
4. **Install PDF library**: For proper PDF output generation
5. **Monitor costs**: If using OpenAI, track API usage

## Support

For detailed documentation, see `CAREER_TOOLS_README.md`

For issues:

1. Check PHP error logs
2. Check browser console for JavaScript errors
3. Verify all files uploaded correctly
4. Test with minimal input first

## Production Deployment

Before going live:

- [ ] Set proper error handling (disable display_errors)
- [ ] Configure production API keys
- [ ] Set up email delivery
- [ ] Install PDF generation library
- [ ] Test all upload limits
- [ ] Set up automated backups
- [ ] Configure HTTPS
- [ ] Test on production server
