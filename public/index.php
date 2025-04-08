<?php

use App\Controller\CompanyController;
use App\Controller\EmployeeController;
use App\Database\MySQLDatabase;

require_once __DIR__ . '/../vendor/autoload.php';

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

$mysqli = waitForDatabase($host, $user, $pass, $db, $port);
$database = new MySQLDatabase($mysqli);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

try {
    if ($uri === '' || $uri === '/') {
        readfile(__DIR__ . '/app.html');
        exit;
    }

    if (preg_match('#^/employees#', $uri)) {
        $controller = new EmployeeController($database, $method, $uri);
        $controller->dispatch();
        exit;
    }

    if (preg_match('#^/companies#', $uri)) {
        $controller = new CompanyController($database, $method, $uri);
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

function waitForDatabase($host, $user, $pass, $db, $port, $attempts = 10, $delay = 1): mysqli
{
    for ($i = 0; $i < $attempts; $i++) {
        try {
            $mysqli = @new mysqli($host, $user, $pass, $db, $port);
            if ($mysqli->connect_errno === 0) {
                return $mysqli;
            }
        } catch (Throwable $e) {
        }

        sleep($delay);
    }

    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection failed after multiple attempts.'
    ]);
    exit;
}
