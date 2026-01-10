# Career Tools - TCPDF Installation Guide

## Option 1: Install TCPDF via Composer (Recommended)

### Step 1: Check if Composer is installed

```powershell
composer --version
```

If not installed, download from: https://getcomposer.org/download/

### Step 2: Navigate to your project directory

```powershell
cd C:\xampp\htdocs\chronuswebsite-main
```

### Step 3: Install TCPDF

```powershell
composer require tecnickcom/tcpdf
```

This will create a `vendor/` directory with TCPDF installed.

---

## Option 2: Manual Installation (If Composer not available)

### Step 1: Download TCPDF

Download from: https://github.com/tecnickcom/TCPDF/releases/latest

### Step 2: Extract to your project

Extract the ZIP file to: `C:\xampp\htdocs\chronuswebsite-main\tcpdf\`

### Step 3: Update PHP files to include TCPDF manually

Add this to the top of `process_cv_revamp.php` and `process_sop_generation.php` (after PHPMailer includes):

```php
// Load TCPDF (manual installation)
if (file_exists(__DIR__ . '/tcpdf/tcpdf.php')) {
    require_once __DIR__ . '/tcpdf/tcpdf.php';
} elseif (file_exists(__DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php')) {
    require_once __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php';
}
```

---

## Option 3: Use Without TCPDF (Fallback Mode)

The system will automatically work without TCPDF by saving files as plain text with `.pdf` extension. This is not ideal for production but works for testing.

To improve this fallback, you can use PHP's built-in functions:

### Alternative: Use FPDF (Lighter than TCPDF)

```powershell
composer require setasign/fpdf
```

Or download from: http://www.fpdf.org/

---

## Verification

After installation, test if TCPDF is available:

```php
<?php
// Test TCPDF installation
if (class_exists('TCPDF')) {
    echo "TCPDF is installed and ready!";
} else {
    echo "TCPDF is NOT available. Using fallback mode.";
}
?>
```

Save this as `test_tcpdf.php` in your project root and access via browser:
`http://localhost/chronuswebsite-main/test_tcpdf.php`

---

## Recommended: Use Composer

Composer is the easiest and most maintainable approach. It also allows you to easily install other packages like the PDF parser mentioned in the setup guide:

```powershell
# Install both TCPDF and PDF parser
composer require tecnickcom/tcpdf
composer require smalot/pdfparser
```

---

## After Installation

Once TCPDF is installed, the system will automatically use it for PDF generation. The files are already configured to:

1. Check if TCPDF is available
2. Use it if present
3. Fall back to plain text if not

No additional code changes needed!
