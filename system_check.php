<?php
/**
 * System Check - Verify all components are properly installed
 * 
 * Access this file via: http://localhost/chronuswebsite-main/system_check.php
 */

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Tools - System Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #2c3e50; border-bottom: 3px solid #9acd32; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; }
        .check { padding: 15px; margin: 10px 0; border-radius: 6px; display: flex; align-items: center; }
        .check.success { background: #d4edda; border-left: 4px solid #28a745; }
        .check.warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .check.error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .icon { font-size: 24px; margin-right: 15px; }
        .success .icon { color: #28a745; }
        .warning .icon { color: #ffc107; }
        .error .icon { color: #dc3545; }
        .details { margin-left: 39px; font-size: 14px; color: #666; margin-top: 5px; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .summary { background: #e8f5e9; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🔧 Career Tools System Check</h1>
    <p>This page verifies that all required components for the CV Revamp and SOP Generator are properly installed and configured.</p>

    <?php
    $checks = [];
    $errors = 0;
    $warnings = 0;
    $success = 0;

    // Check 1: PHP Version
    $php_version = phpversion();
    if (version_compare($php_version, '7.0.0', '>=')) {
        $checks[] = ['status' => 'success', 'title' => 'PHP Version', 'message' => "PHP $php_version (Minimum 7.0 required)", 'icon' => '✓'];
        $success++;
    } else {
        $checks[] = ['status' => 'error', 'title' => 'PHP Version', 'message' => "PHP $php_version - Please upgrade to PHP 7.0 or higher", 'icon' => '✗'];
        $errors++;
    }

    // Check 2: Required Extensions
    $required_extensions = ['json', 'curl', 'zip', 'mbstring'];
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext)) {
            $checks[] = ['status' => 'success', 'title' => "PHP Extension: $ext", 'message' => 'Loaded', 'icon' => '✓'];
            $success++;
        } else {
            $checks[] = ['status' => 'error', 'title' => "PHP Extension: $ext", 'message' => 'Not loaded - Please enable in php.ini', 'icon' => '✗'];
            $errors++;
        }
    }

    // Check 3: Upload Directories
    $upload_dirs = [
        'data/cv_uploads' => 'CV Uploads Directory',
        'data/sop_uploads' => 'SOP Uploads Directory'
    ];
    foreach ($upload_dirs as $dir => $name) {
        $path = __DIR__ . '/' . $dir;
        if (file_exists($path) && is_dir($path)) {
            if (is_writable($path)) {
                $checks[] = ['status' => 'success', 'title' => $name, 'message' => "Exists and writable: $dir", 'icon' => '✓'];
                $success++;
            } else {
                $checks[] = ['status' => 'error', 'title' => $name, 'message' => "Directory exists but not writable: $dir", 'icon' => '✗'];
                $errors++;
            }
        } else {
            $checks[] = ['status' => 'warning', 'title' => $name, 'message' => "Directory missing - will be created automatically: $dir", 'icon' => '⚠'];
            $warnings++;
        }
    }

    // Check 4: PHPMailer
    $phpmailer_path = __DIR__ . '/PHPMailer/src/PHPMailer.php';
    if (file_exists($phpmailer_path)) {
        require_once $phpmailer_path;
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $checks[] = ['status' => 'success', 'title' => 'PHPMailer', 'message' => 'Installed and ready for email delivery', 'icon' => '✓'];
            $success++;
        } else {
            $checks[] = ['status' => 'error', 'title' => 'PHPMailer', 'message' => 'Files found but class not loading properly', 'icon' => '✗'];
            $errors++;
        }
    } else {
        $checks[] = ['status' => 'error', 'title' => 'PHPMailer', 'message' => 'Not found - Email delivery will not work', 'icon' => '✗'];
        $errors++;
    }

    // Check 5: TCPDF
    $tcpdf_paths = [
        __DIR__ . '/vendor/autoload.php',
        __DIR__ . '/tcpdf/tcpdf.php',
        __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php'
    ];
    $tcpdf_found = false;
    foreach ($tcpdf_paths as $tcpdf_path) {
        if (file_exists($tcpdf_path)) {
            require_once $tcpdf_path;
            if (class_exists('TCPDF')) {
                $checks[] = ['status' => 'success', 'title' => 'TCPDF (PDF Generation)', 'message' => 'Installed - Professional PDF output enabled', 'icon' => '✓'];
                $success++;
                $tcpdf_found = true;
                break;
            }
        }
    }
    if (!$tcpdf_found) {
        $checks[] = ['status' => 'warning', 'title' => 'TCPDF (PDF Generation)', 'message' => 'Not installed - Will use basic text fallback. Install with: composer require tecnickcom/tcpdf', 'icon' => '⚠'];
        $warnings++;
    }

    // Check 6: Configuration File
    if (file_exists(__DIR__ . '/config.php')) {
        require_once __DIR__ . '/config.php';
        if (defined('OPENAI_API_KEY')) {
            $api_key = OPENAI_API_KEY;
            if (!empty($api_key) && $api_key !== 'your-api-key-here') {
                $checks[] = ['status' => 'success', 'title' => 'Configuration File', 'message' => 'config.php found with API key configured', 'icon' => '✓'];
                $success++;
            } else {
                $checks[] = ['status' => 'warning', 'title' => 'Configuration File', 'message' => 'config.php found but API key not set - Will use rule-based processing', 'icon' => '⚠'];
                $warnings++;
            }
        } else {
            $checks[] = ['status' => 'warning', 'title' => 'Configuration File', 'message' => 'config.php found but incomplete', 'icon' => '⚠'];
            $warnings++;
        }
    } elseif (file_exists(__DIR__ . '/config.example.php')) {
        $checks[] = ['status' => 'warning', 'title' => 'Configuration File', 'message' => 'Using config.example.php - Copy to config.php and configure for production', 'icon' => '⚠'];
        $warnings++;
    } else {
        $checks[] = ['status' => 'warning', 'title' => 'Configuration File', 'message' => 'No configuration file - Using default settings', 'icon' => '⚠'];
        $warnings++;
    }

    // Check 7: Core Files
    $core_files = [
        'cv-tools.php' => 'Career Tools Page',
        'process_cv_revamp.php' => 'CV Revamp Processor',
        'process_sop_generation.php' => 'SOP Generator Processor'
    ];
    foreach ($core_files as $file => $name) {
        if (file_exists(__DIR__ . '/' . $file)) {
            $checks[] = ['status' => 'success', 'title' => $name, 'message' => "File exists: $file", 'icon' => '✓'];
            $success++;
        } else {
            $checks[] = ['status' => 'error', 'title' => $name, 'message' => "File missing: $file", 'icon' => '✗'];
            $errors++;
        }
    }

    // Display checks
    echo '<div class="section">';
    echo '<h2>Component Status</h2>';
    foreach ($checks as $check) {
        echo '<div class="check ' . $check['status'] . '">';
        echo '<span class="icon">' . $check['icon'] . '</span>';
        echo '<div>';
        echo '<strong>' . htmlspecialchars($check['title']) . '</strong><br>';
        echo '<div class="details">' . htmlspecialchars($check['message']) . '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';

    // Summary
    $total = $success + $warnings + $errors;
    echo '<div class="summary">';
    echo '<h2>Summary</h2>';
    echo '<p><strong>Total Checks:</strong> ' . $total . '</p>';
    echo '<p style="color: #28a745;"><strong>✓ Passed:</strong> ' . $success . '</p>';
    echo '<p style="color: #ffc107;"><strong>⚠ Warnings:</strong> ' . $warnings . '</p>';
    echo '<p style="color: #dc3545;"><strong>✗ Errors:</strong> ' . $errors . '</p>';
    
    if ($errors > 0) {
        echo '<p style="margin-top: 20px; padding: 15px; background: #f8d7da; border-radius: 6px; color: #721c24;">';
        echo '<strong>Action Required:</strong> Please fix the errors above before using the Career Tools.';
        echo '</p>';
    } elseif ($warnings > 0) {
        echo '<p style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 6px; color: #856404;">';
        echo '<strong>Recommended:</strong> Address the warnings above for optimal functionality.';
        echo '</p>';
    } else {
        echo '<p style="margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 6px; color: #155724;">';
        echo '<strong>All Systems Ready!</strong> The Career Tools are fully configured and ready to use.';
        echo '</p>';
    }
    echo '</div>';

    // Installation Instructions
    echo '<div class="section">';
    echo '<h2>Quick Setup Instructions</h2>';
    echo '<h3>1. Install TCPDF (Recommended)</h3>';
    echo '<p><code>composer require tecnickcom/tcpdf</code></p>';
    echo '<p>Or see <a href="INSTALL_TCPDF.md">INSTALL_TCPDF.md</a> for manual installation</p>';
    
    echo '<h3>2. Configure API Key (Optional)</h3>';
    echo '<p>Copy <code>config.example.php</code> to <code>config.php</code> and set your OpenAI API key:</p>';
    echo '<p><code>define(\'OPENAI_API_KEY\', \'sk-your-actual-key-here\');</code></p>';
    
    echo '<h3>3. Configure Email (Optional)</h3>';
    echo '<p>In <code>config.php</code>, set:</p>';
    echo '<p><code>define(\'EMAIL_ENABLED\', true);</code><br>';
    echo '<code>define(\'SMTP_USERNAME\', \'your-email@gmail.com\');</code><br>';
    echo '<code>define(\'SMTP_PASSWORD\', \'your-app-password\');</code></p>';
    
    echo '<h3>4. Test the System</h3>';
    echo '<p>Navigate to <a href="cv-tools.php">cv-tools.php</a> and try uploading a test CV</p>';
    echo '</div>';

    echo '<div class="section">';
    echo '<h2>Documentation</h2>';
    echo '<ul>';
    echo '<li><a href="CAREER_TOOLS_README.md">CAREER_TOOLS_README.md</a> - Complete technical documentation</li>';
    echo '<li><a href="SETUP_GUIDE.md">SETUP_GUIDE.md</a> - Step-by-step setup guide</li>';
    echo '<li><a href="INSTALL_TCPDF.md">INSTALL_TCPDF.md</a> - TCPDF installation instructions</li>';
    echo '<li><a href="CHECKLIST.md">CHECKLIST.md</a> - Testing and deployment checklist</li>';
    echo '</ul>';
    echo '</div>';
    ?>

    <p style="text-align: center; margin-top: 40px; color: #999; font-size: 14px;">
        Chronus Career Tools v1.0 | System Check Page
    </p>
</body>
</html>
