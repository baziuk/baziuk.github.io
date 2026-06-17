<?php
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    exit;
}

if (!verifyToken()) json_response(['error' => 'Unauthorized'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);

$body = json_decode(file_get_contents('php://input'), true);
$id   = $body['id'] ?? '';
if (!$id) json_response(['error' => 'ID required'], 400);

$projects = load_projects();
$filtered = array_values(array_filter($projects, fn($p) => $p['id'] !== $id));
save_projects($filtered);
json_response(['ok' => true, 'remaining' => count($filtered)]);
