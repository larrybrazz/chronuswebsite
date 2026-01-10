# 🔧 Fix "Can't Open File" Error - Quick Solutions

## Problem

When you click download links for CV or SOP, you get: "We can't open this file - Something went wrong"

## Cause

The files are being created as plain text with `.pdf` extension because TCPDF isn't installed yet. Your browser/system can't open them as PDFs.

---

## ✅ SOLUTION 1: Install TCPDF (Recommended - 2 minutes)

### Windows (Double-click method):

1. **Double-click** `install_tcpdf.bat` in your project folder
2. Wait for installation to complete
3. Try downloading again - will now be proper PDFs!

### Or use PowerShell:

```powershell
cd C:\xampp\htdocs\chronuswebsite-main
composer require tecnickcom/tcpdf
```

### Verify Installation:

Visit: `http://localhost/chronuswebsite-main/system_check.php`

- Should show TCPDF with green checkmark ✓

---

## ✅ SOLUTION 2: Download Works Now (Even Without TCPDF)

I've fixed the download system! Even without TCPDF:

- Files now save as `.txt` instead of fake `.pdf`
- Downloads use proper headers (force download)
- Files open correctly in any text editor

### What Changed:

- Created `download_file.php` - proper download handler
- Files automatically save as `.txt` if no TCPDF
- Correct MIME types for all file types

### Try Again:

1. Go to `cv-tools.php`
2. Upload a test CV
3. Click download - should download as `.txt` file
4. Open in Notepad/any text editor - works!

---

## 📊 Comparison

| Feature    | Without TCPDF | With TCPDF       |
| ---------- | ------------- | ---------------- |
| File Type  | `.txt`        | `.pdf`           |
| Download   | ✅ Works      | ✅ Works         |
| Formatting | Plain text    | Professional PDF |
| Opens in   | Notepad       | PDF Reader       |
| Email      | ✅ Works      | ✅ Works         |

---

## 🚀 Quick Test

### Test Current Status:

```
http://localhost/chronuswebsite-main/system_check.php
```

### Test Downloads:

1. Visit: `http://localhost/chronuswebsite-main/cv-tools.php`
2. Upload any CV file
3. Fill in job title
4. Click "Generate"
5. Click download link
6. Should download successfully!

---

## 💡 Installation Notes

### If Composer Not Installed:

1. Download from: https://getcomposer.org/download/
2. Run installer
3. Restart PowerShell/Command Prompt
4. Then run: `composer require tecnickcom/tcpdf`

### Alternative - Manual TCPDF Install:

1. Download: https://github.com/tecnickcom/TCPDF/releases
2. Extract to: `C:\xampp\htdocs\chronuswebsite-main\tcpdf\`
3. Files should auto-detect and use it

---

## 🔍 Troubleshooting

### Still Getting "Can't Open File"?

**Check download URL:**

- Old broken URL: `data/cv_uploads/filename.pdf`
- New working URL: `download_file.php?file=filename.txt&type=cv`

**Clear browser cache:**

```
Ctrl + Shift + Delete (Chrome/Firefox)
Clear cached images and files
```

**Check file was created:**

```powershell
# Check if files exist
ls C:\xampp\htdocs\chronuswebsite-main\data\cv_uploads\
ls C:\xampp\htdocs\chronuswebsite-main\data\sop_uploads\
```

**Check PHP errors:**

```powershell
# Check XAMPP error log
Get-Content C:\xampp\php\logs\php_error_log -Tail 20
```

---

## ✨ What Happens Now

### With TCPDF Installed:

1. Upload CV → ✓ Works
2. Generate revamp → ✓ Creates professional PDF
3. Click download → ✓ Downloads as proper PDF
4. Open file → ✓ Opens in PDF reader with formatting

### Without TCPDF (Fallback):

1. Upload CV → ✓ Works
2. Generate revamp → ✓ Creates formatted text
3. Click download → ✓ Downloads as .txt file
4. Open file → ✓ Opens in text editor (readable!)

Both ways work! TCPDF just makes it prettier.

---

## 🎯 Recommended Action

**Option A - Full Production Setup (5 min):**

1. Double-click `install_tcpdf.bat`
2. Copy `config.example.php` to `config.php`
3. Add your API key (optional)
4. Done! Professional PDFs enabled

**Option B - Test Now, Setup Later:**

1. Just try uploading again
2. Downloads will work as .txt files
3. Install TCPDF when ready for production
4. Everything else works fine!

---

## 📝 Files Created for This Fix

- `download_file.php` - Secure download handler with proper headers
- `install_tcpdf.bat` - One-click TCPDF installer for Windows
- Files now auto-detect TCPDF and adjust file extension

No other changes needed - system will work with or without TCPDF!
