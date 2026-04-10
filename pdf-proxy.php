<?php
// PDF Proxy - Serves PDFs same-origin to avoid CORS issues
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_token'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Get PDF path from query string
$module = $_GET['module'] ?? '';
$file = $_GET['file'] ?? '';

// Validate module and file
$allowedModules = ['new_spotters_pdf', 'theory_notes_pdf', 'new_osce_pdf', 
                   'new_exam_cases_pdf', 'new_table_viva_pdf', 'watch_and_learn_pdf'];

if (!in_array($module, $allowedModules)) {
    http_response_code(400);
    exit('Invalid module');
}

// Sanitize filename to prevent directory traversal
$file = basename($file);
if (empty($file) || !preg_match('/\.pdf$/i', $file)) {
    http_response_code(400);
    exit('Invalid file');
}

// Construct file path
$filePath = __DIR__ . '/admin/assets/admin/images/' . $module . '/' . $file;

// Check if file exists
if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File not found');
}

// Set headers for PDF
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($filePath));
header('Content-Disposition: inline; filename="' . $file . '"');
header('Accept-Ranges: bytes');

// Handle range requests for PDF.js
if (isset($_SERVER['HTTP_RANGE'])) {
    $size = filesize($filePath);
    $range = $_SERVER['HTTP_RANGE'];
    $range = str_replace('bytes=', '', $range);
    $range = explode('-', $range);
    $start = intval($range[0]);
    $end = isset($range[1]) && is_numeric($range[1]) ? intval($range[1]) : $size - 1;
    
    header('HTTP/1.1 206 Partial Content');
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
    header('Content-Length: ' . ($end - $start + 1));
    
    $fp = fopen($filePath, 'rb');
    fseek($fp, $start);
    echo fread($fp, $end - $start + 1);
    fclose($fp);
} else {
    // Send entire file
    readfile($filePath);
}
