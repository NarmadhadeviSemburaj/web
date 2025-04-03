<?php
header("Content-Type: image/jpeg");

// Database configuration
require_once __DIR__ . '/../db_config.php';

// Security check - only allow requests from your domain
$allowed_referers = ['yourdomain.com']; // Replace with your domain
$referer = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_HOST);
if (!in_array($referer, $allowed_referers)) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// Get the image path from query parameter
if (!isset($_GET['path'])) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

$requestedPath = $_GET['path'];
$basePath = realpath('uploads');

// Validate the path to prevent directory traversal
$fullPath = realpath($requestedPath);
if ($fullPath === false || strpos($fullPath, $basePath) !== 0) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// Check if file exists and is readable
if (!file_exists($fullPath) || !is_readable($fullPath)) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// Get file info
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($fullPath);

// Set appropriate headers and output the image
header("Content-Type: " . $mimeType);
header("Content-Length: " . filesize($fullPath));

readfile($fullPath);
exit;
?>