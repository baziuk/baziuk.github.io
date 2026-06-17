<?php
// Змініть пароль перед деплоєм!
define('ADMIN_PASSWORD', 'SpetsInstal2024!');
define('ADMIN_HASH', hash('sha256', ADMIN_PASSWORD));
define('SECRET_KEY', 'si_secret_XkQ9#mL2pR7');
define('TOKEN', hash('sha256', ADMIN_HASH . SECRET_KEY));
define('DATA_FILE', __DIR__ . '/../data/projects.json');
define('UPLOAD_DIR', __DIR__ . '/../img/uploads/');
define('UPLOAD_URL', '/img/uploads/');

function verifyToken(): bool {
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($auth, 'Bearer ')) return false;
    return hash_equals(TOKEN, substr($auth, 7));
}

function json_response(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function load_projects(): array {
    if (!file_exists(DATA_FILE)) return [];
    $raw = file_get_contents(DATA_FILE);
    return json_decode($raw, true) ?? [];
}

function save_projects(array $projects): void {
    $dir = dirname(DATA_FILE);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents(DATA_FILE, json_encode($projects, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
