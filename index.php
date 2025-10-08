<?php
// index.php

header('Content-Type: application/json');

$config = require __DIR__ . '/config/database.php';

// koneksi ke database
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

require_once __DIR__ . '/app/controllers/TodoController.php';
$controller = new TodoController($pdo);

// Ambil URI tanpa base folder (otomatis deteksi)
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim(str_replace($script_name, '', $request_uri), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Routing API
if ($uri === 'todos' && $method === 'GET') {
    $controller->index();

} elseif (preg_match('#^todos/([A-Za-z0-9\-_]+)$#', $uri, $matches) && $method === 'GET') {
    // GET /todos/{id}
    $controller->show($matches[1]);

} elseif ($uri === 'todos' && $method === 'POST') {
    // POST /todos
    $controller->store();

} elseif (preg_match('#^todos/([A-Za-z0-9\-_]+)$#', $uri, $matches) && $method === 'PUT') {
    // PUT /todos/{id}
    $controller->update($matches[1]);

} elseif (preg_match('#^todos/([A-Za-z0-9\-_]+)$#', $uri, $matches) && $method === 'DELETE') {
    // DELETE /todos/{id}
    $controller->delete($matches[1]);

} else {
    // Endpoint tidak ditemukan
    http_response_code(404);
    echo json_encode([
        'error' => 'Endpoint not found',
        'uri' => $uri,
        'method' => $method
    ]);
}
