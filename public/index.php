<?php

use App\Controller\EmployeeController;

require_once __DIR__ . '/../vendor/autoload.php';

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

$mysqli = new mysqli($host, $user, $pass, $db, $port);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

try {
    if ($uri === '' || $uri === '/') {
        readfile(__DIR__ . '/app.html');
        exit;
    }

    if (preg_match('#^/employees#', $uri)) {
        $controller = new EmployeeController($mysqli, $method, $uri);
        $controller->dispatch();
        exit;
    }

    http_response_code(404);
    readfile(__DIR__ . '/404.html');
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
