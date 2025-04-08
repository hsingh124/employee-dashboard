<?php

namespace App\Repository;

use App\Database\DatabaseInterface;

/**
 * Handles queries related to employee-level data.
 */
class EmployeeRepository {
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Inserts a new employee record into the database.
     * 
     * @param array $data The employee data (expects keys: company_name, employee_name, email_address, salary).
     * 
     * @return void
     */
    public function insert(array $data): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO employee (company_name, employee_name, email_address, salary)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sssi',
            $data['company_name'],
            $data['employee_name'],
            $data['email_address'],
            $data['salary']
        );

        $stmt->execute();
    }

    /**
     * Fetches all employees from the database.
     * 
     * @return array A list of employee records as associative arrays.
     */
    public function findAll(): array
    {
        $result = $this->db->query("SELECT * FROM employee");

        if (!$result) {
            throw new \RuntimeException("Database error: " . $this->db->getError());
        }

        $employees = [];

        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    
        return $employees;
    }

    /**
     * Updates an employee's email address by their ID.
     * 
     * @param int $id The employee's ID.
     * @param string $newEmail The new email address to set.
     * 
     * @return void
     */
    public function updateEmail(int $id, string $newEmail): void
    {
        $stmt = $this->db->prepare(
            "UPDATE employee SET email_address = ? WHERE id = ?"
        );

        if (!$stmt) {
            throw new \RuntimeException("Prepare failed: " . $this->db->getError());
        }

        $stmt->bind_param('si', $newEmail, $id);

        if (!$stmt->execute()) {
            throw new \RuntimeException("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    }
}