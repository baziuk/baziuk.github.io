<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    exit;
}

header('Access-Control-Allow-Origin: *');

if (!verifyToken()) json_response(['error' => 'Unauthorized'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
if (empty($_FILES['file'])) json_response(['error' => 'No file'], 400);

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) json_response(['error' => 'Upload error ' . $file['error']], 500);

$maxSize = 50 * 1024 * 1024; // 50MB
if ($file['size'] > $maxSize) json_response(['error' => 'File too large (max 50MB)'], 400);

$mime = mime_content_type($file['tmp_name']);
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm'];
if (!in_array($mime, $allowed)) json_response(['error' => 'File type not allowed: ' . $mime], 400);

if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

$ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
$name = uniqid('', true) . '.' . strtolower($ext);
$dest = UPLOAD_DIR . $name;

if (!move_uploaded_file($file['tmp_name'], $dest)) json_response(['error' => 'Failed to move file'], 500);

json_response(['url' => UPLOAD_URL . $name, 'name' => $name]);
