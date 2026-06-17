<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);

$body = json_decode(file_get_contents('php://input'), true);
$password = $body['password'] ?? '';

if (hash_equals(ADMIN_HASH, hash('sha256', $password))) {
    json_response(['token' => TOKEN]);
} else {
    json_response(['error' => 'Невірний пароль'], 401);
}
