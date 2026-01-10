@echo off
REM Quick TCPDF Installer for Windows
REM Run this file to install TCPDF via Composer

echo ========================================
echo  TCPDF Quick Installer
echo ========================================
echo.

REM Check if Composer is installed
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer is not installed!
    echo.
    echo Please install Composer first from:
    echo https://getcomposer.org/download/
    echo.
    pause
    exit /b 1
)

echo [OK] Composer found!
echo.

REM Navigate to project directory
cd /d "%~dp0"
echo Current directory: %CD%
echo.

REM Install TCPDF
echo Installing TCPDF...
echo.
composer require tecnickcom/tcpdf

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo  Installation Complete!
    echo ========================================
    echo.
    echo TCPDF has been installed successfully.
    echo.
    echo Next steps:
    echo 1. Refresh system_check.php to verify installation
    echo 2. Try uploading a CV again
    echo 3. Downloads should now work as PDFs
    echo.
) else (
    echo.
    echo ========================================
    echo  Installation Failed
    echo ========================================
    echo.
    echo Something went wrong. Please try manually:
    echo   composer require tecnickcom/tcpdf
    echo.
)

pause
