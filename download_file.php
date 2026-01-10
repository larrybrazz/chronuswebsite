<?php
/**
 * Secure File Download Handler
 * Handles downloading of generated CV and SOP files with proper MIME types
 */

// Get parameters
$filename = isset($_GET['file']) ? $_GET['file'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'cv';

// Validate inputs
if (empty($filename)) {
    http_response_code(400);
    die('Error: No file specified');
}

// Sanitize filename to prevent directory traversal
$filename = basename($filename);

// Determine upload directory based on type
$upload_dir = ($type === 'sop') 
    ? __DIR__ . '/data/sop_uploads/' 
    : __DIR__ . '/data/cv_uploads/';

$filepath = $upload_dir . $filename;

// Check if file exists
if (!file_exists($filepath)) {
    http_response_code(404);
    die('Error: File not found');
}

// Determine MIME type based on extension
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$mime_types = [
    'pdf' => 'application/pdf',
    'txt' => 'text/plain',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

$mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';

// Determine download filename
$download_name = ($type === 'sop') ? 'Statement_of_Purpose' : 'Revamped_CV';
$download_name .= '.' . $extension;

// Clear any output buffers
if (ob_get_level()) {
    ob_end_clean();
}

// Send headers for download
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $download_name . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Output file
readfile($filepath);
exit;
