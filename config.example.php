<?php
/**
 * Career Tools Configuration
 * 
 * Copy this file to config.php and update with your actual values
 * DO NOT commit config.php to version control
 */

// ===========================================
// AI API CONFIGURATION
// ===========================================

// OpenAI Configuration (Default)
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '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');
define('OPENAI_MODEL', 'gpt-4'); // Options: gpt-4, gpt-3.5-turbo
define('OPENAI_ENDPOINT', 'https://api.openai.com/v1/chat/completions');

// Alternative: Claude (Anthropic)
// define('CLAUDE_API_KEY', getenv('CLAUDE_API_KEY') ?: '');
// define('CLAUDE_MODEL', 'claude-3-opus-20240229');
// define('CLAUDE_ENDPOINT', 'https://api.anthropic.com/v1/messages');

// ===========================================
// AI PARAMETERS
// ===========================================

// CV Revamp Settings
define('CV_TEMPERATURE', 0.3);        // Lower = more factual
define('CV_MAX_TOKENS', 2000);
define('CV_TOP_P', 1.0);

// SOP Generation Settings
define('SOP_TEMPERATURE', 0.4);       // Balanced for narrative
define('SOP_MAX_TOKENS', 1500);
define('SOP_TOP_P', 1.0);

// ===========================================
// FILE UPLOAD SETTINGS
// ===========================================

define('MAX_FILE_SIZE', 10 * 1024 * 1024);  // 10MB in bytes
define('ALLOWED_CV_EXTENSIONS', ['pdf', 'doc', 'docx']);
define('CV_UPLOAD_DIR', __DIR__ . '/data/cv_uploads');
define('SOP_UPLOAD_DIR', __DIR__ . '/data/sop_uploads');

// ===========================================
// EMAIL CONFIGURATION
// ===========================================

define('EMAIL_ENABLED', false);       // Set to true when SMTP configured
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');     // Options: tls, ssl
define('FROM_EMAIL', 'noreply@chronus.com');
define('FROM_NAME', 'Chronus Career Services');

// ===========================================
// PDF GENERATION
// ===========================================

define('PDF_LIBRARY', 'basic');       // Options: basic, tcpdf, dompdf
define('PDF_PAGE_SIZE', 'A4');
define('PDF_ORIENTATION', 'portrait');
define('PDF_FONT', 'helvetica');
define('PDF_FONT_SIZE', 11);

// ===========================================
// PROCESSING OPTIONS
// ===========================================

define('ENABLE_AI_PROCESSING', true);  // Set false to use only rule-based
define('AI_TIMEOUT', 30);             // Seconds to wait for AI response
define('ENABLE_FALLBACK', true);      // Use rule-based if AI fails

// ===========================================
// LOGGING
// ===========================================

define('ENABLE_LOGGING', true);
define('LOG_FILE', __DIR__ . '/data/career_tools.log');
define('LOG_LEVEL', 'INFO');          // Options: DEBUG, INFO, WARNING, ERROR

// ===========================================
// SECURITY
// ===========================================

define('ENABLE_RATE_LIMITING', true);
define('MAX_REQUESTS_PER_HOUR', 10);  // Per IP address
define('ENABLE_FILE_SCAN', false);    // Requires ClamAV or similar
define('DELETE_FILES_AFTER_DAYS', 30);

// ===========================================
// FEATURE FLAGS
// ===========================================

define('ENABLE_CV_REVAMP', true);
define('ENABLE_SOP_GENERATION', true);
define('ENABLE_DOWNLOAD', true);
define('ENABLE_EMAIL_DELIVERY', false);  // Set true when email configured
define('ENABLE_PREVIEW', false);         // Future: show preview before download

// ===========================================
// HELPER FUNCTIONS
// ===========================================

/**
 * Get configuration value with fallback
 */
function get_config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Check if AI is properly configured
 */
function is_ai_configured() {
    $api_key = OPENAI_API_KEY;
    return !empty($api_key) && $api_key !== 'your-api-key-here';
}

/**
 * Check if email is properly configured
 */
function is_email_configured() {
    return EMAIL_ENABLED && 
           SMTP_USERNAME !== 'your-email@gmail.com' &&
           !empty(SMTP_PASSWORD);
}

/**
 * Log message to file (if logging enabled)
 */
function log_message($level, $message, $context = []) {
    if (!ENABLE_LOGGING) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $context_str = !empty($context) ? json_encode($context) : '';
    $log_entry = "[$timestamp] [$level] $message $context_str\n";
    
    file_put_contents(LOG_FILE, $log_entry, FILE_APPEND);
}

// ===========================================
// VALIDATION
// ===========================================

// Ensure upload directories exist
if (!file_exists(CV_UPLOAD_DIR)) {
    mkdir(CV_UPLOAD_DIR, 0755, true);
}
if (!file_exists(SOP_UPLOAD_DIR)) {
    mkdir(SOP_UPLOAD_DIR, 0755, true);
}

// Ensure log directory exists
if (ENABLE_LOGGING && !file_exists(dirname(LOG_FILE))) {
    mkdir(dirname(LOG_FILE), 0755, true);
}
