<?php

namespace App\Controller;

use App\Database\DatabaseInterface;
use App\Repository\EmployeeRepository;

/**
 * EmployeeController handles all employee-related routes and actions.
 */
class EmployeeController extends BaseController {
    private EmployeeRepository $repository;

    public function __construct(DatabaseInterface $db, string $requestMethod, string $requestUri)
    {
        parent::__construct($requestMethod, $requestUri);
        $this->repository = new EmployeeRepository($db);
    }

    /**
     * Dispatches employee-related routes.
     * 
     * @param array $routes
     * 
     * @return void
     */
    public function dispatch(array $routes = []): void 
    {
        if (empty($routes)) {
            $routes = [
                'POST /employees/import-from-csv' => 'importFromCsv',
                'GET /employees' => 'getAllEmployees',
                'PATCH /employees/{id}/email' => 'updateEmail',
            ];
        }

        parent::dispatch($routes);
    }

    /**
     * Imports employee data from a CSV file uploaded via multipart/form-data.
     * 
     * @return array JSON response indicating success or error.
     */
    protected function importFromCsv(): array
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

    /**
     * Retrieves and returns all employees from the database.
     * 
     * @return array JSON response with a list of employees.
     */
    protected function getAllEmployees(): array
    {
        $employees = $this->repository->findAll();

        return [
            'status' => 200,
            'body' => $employees
        ];
    }

    /**
     * Updates an employee's email address based on the ID in the URI and JSON body.
     * 
     * @return array JSON response indicating success or validation error.
     */
    protected function updateEmail(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $this->extractEmployeeId();

        $validationError = $this->validateUpdateEmailRequest($input, $id);
        if ($validationError !== null) {
            return $validationError;
        }

        $this->repository->updateEmail($id, $input['email_address']);

        return [
            'status' => 200,
            'body' => ['message' => 'Email updated']
        ];
    }

    /**
     * Validates the request data for updating an email address.
     * 
     * @param array $input
     * @param int|null $id
     * 
     * @return array|null
     */
    protected function validateUpdateEmailRequest(array $input, ?int $id): ?array
    {
        if (empty($id)) {
            return [
                'status' => 422,
                'body' => ['error' => 'Employee ID is missing or invalid in the URL']
            ];
        }

        if (empty($input['email_address'])) {
            return [
                'status' => 422,
                'body' => ['error' => 'Missing email_address']
            ];
        }

        return null;
    }

    /**
     * Extracts the employee ID from the request URI.
     * 
     * @return int|null The employee ID or null if not found.
     */
    protected function extractEmployeeId(): ?int
    {
        if (preg_match('#^/employees/(\d+)#', $this->requestUri, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }
}
