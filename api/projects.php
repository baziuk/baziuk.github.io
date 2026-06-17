<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    exit;
}

header('Access-Control-Allow-Origin: *');

// GET — повернути всі проекти (публічно)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    json_response(load_projects());
}

// POST — зберегти проект (потребує токен)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyToken()) json_response(['error' => 'Unauthorized'], 401);

    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) json_response(['error' => 'Invalid JSON'], 400);

    $projects = load_projects();
    $id = $body['id'] ?? null;

    $project = [
        'id'          => $id ?: uniqid('p', true),
        'title'       => trim($body['title'] ?? ''),
        'location'    => trim($body['location'] ?? ''),
        'category'    => trim($body['category'] ?? ''),
        'description' => trim($body['description'] ?? ''),
        'photo'       => $body['photo'] ?? '',
        'video'       => $body['video'] ?: null,
        'updatedAt'   => date('c'),
    ];

    if (!$project['title']) json_response(['error' => 'Title required'], 400);

    $idx = array_search($id, array_column($projects, 'id'));
    if ($idx !== false) {
        $projects[$idx] = $project; // update
    } else {
        $project['createdAt'] = date('c');
        array_unshift($projects, $project); // insert at top
    }

    save_projects($projects);
    json_response($project);
}

json_response(['error' => 'Method not allowed'], 405);
