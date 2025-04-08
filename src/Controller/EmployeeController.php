<?php

namespace App\Controller;

use App\Repository\EmployeeRepository;
use mysqli;

class EmployeeController {
    private string $requestMethod;
    private string $requestUri;
    private EmployeeRepository $repository;

    public function __construct(mysqli $db, string $requestMethod, string $requestUri)
    {
        $this->requestMethod = $requestMethod;
        $this->requestUri = rtrim($requestUri, '/');
        $this->repository = new EmployeeRepository($db);
    }

    public function dispatch(): void 
    {
        $routes = [
            'POST /employees/import-from-csv' => 'importFromCsv',
            'GET /employees' => 'getAllEmployees',
            'PATCH /employees/{id}/email' => 'updateEmail',
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

    private function match(string $pattern): bool
    {
        $regex = preg_replace('#\{id\}#', '(\d+)', $pattern);
        return preg_match("#^$regex$#", $this->requestUri);
    }

    private function ensureMethodExists(string $method): void
    {
        if (!method_exists($this, $method)) {
            throw new \RuntimeException("Method {$method} does not exist in " . static::class);
        }
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

    private function importFromCsv(): array
    {
        $csvPath = $_FILES['csv']['tmp_name'] ?? null;

        if (!$csvPath || !file_exists($csvPath)) {
            return [
                'status' => 400,
                'body' => ['error' => 'CSV file is missing or invalid.']
            ];
        }

        $handle = fopen($csvPath, 'r');

        if (!$handle) {
            return [
                'status' => 500,
                'body' => ['error' => 'Unable to open CSV file.']
            ];
        }

        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            [$company, $employeeName, $email, $salary] = $row;

            $this->repository->insert([
                'company_name' => trim($company),
                'employee_name' => trim($employeeName),
                'email_address' => trim($email),
                'salary' => (int) $salary
            ]);
        }

        fclose($handle);

        return [
            'status' => 200,
            'body' => ['message' => 'CSV imported successfully']
        ];
    }

    private function getAllEmployees(): array
    {
        $employees = $this->repository->findAll();

        return [
            'status' => 200,
            'body' => $employees
        ];
    }

    private function updateEmail(): array
    {
        return [
            'status' => 200,
            'body' => ['message' => 'Success']
        ];
    }
}
