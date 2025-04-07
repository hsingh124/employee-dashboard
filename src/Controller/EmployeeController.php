<?php

namespace App\Controller;

use App\Repository\EmployeeRepository;
use mysqli;

class EmployeeController {
    private mysqli $db;
    private string $requestMethod;
    private string $requestUri;
    private EmployeeRepository $repository;

    public function __construct(mysqli $db, string $requestMethod, string $requestUri)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->requestUri = rtrim($requestUri, '/');
        $this->repository = new EmployeeRepository($db);
    }

    public function dispatch(): void 
    {
        $routes = [
            'POST /employees/import-from-csv' => 'importFromCsv',
            'GET /employees' => 'getAllEmployees',
            'PATCH /employees/{id}/email' => 'updateEmailEndpoint',
        ];
        
        foreach ($routes as $route => $method) {
            [$routeMethod, $routePattern] = explode(' ', $route);

            if ($this->tryHandleRoute($routeMethod, $routePattern, $method)) {
                return;
            }
        }

        $this->sendResponse([
            'status' => 404,
            'body' => ['error' => 'Not Found']
        ]);
    }

    private function importFromCsv(): array
    {
        return [
            'status' => 200,
            'body' => ['message' => 'Success']
        ];
    }

    private function getAllEmployees(): array
    {
        return [
            'status' => 200,
            'body' => ['message' => 'Success']
        ];
    }

    private function updateEmailEndpoint(): array
    {
        return [
            'status' => 200,
            'body' => ['message' => 'Success']
        ];
    }

    private function tryHandleRoute(string $routeMethod, string $routePattern, string $handlerMethod): bool
    {
        if ($this->requestMethod === $routeMethod && $this->match($routePattern)) {
            $this->ensureMethodExists($handlerMethod);
            $response = $this->$handlerMethod();
            $this->sendResponse($response);
            return true;
        }

        return false;
    }

    private function ensureMethodExists(string $method): void
    {
        if (!method_exists($this, $method)) {
            throw new \RuntimeException("Method {$method} does not exist in " . static::class);
        }
    }

    private function match(string $pattern): bool
    {
        $regex = preg_replace('#\{id\}#', '(\d+)', $pattern);
        return preg_match("#^$regex$#", $this->requestUri);
    }

    private function sendResponse(array $response): void
    {
        $status = $response['status'] ?? 200;
        $body = $response['body'] ?? null;

        http_response_code($status);
        header('Content-Type: application/json');

        if ($body !== null) {
            echo json_encode($body);
        }
    }
}
