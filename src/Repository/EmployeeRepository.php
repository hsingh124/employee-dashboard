<?php

namespace App\Repository;

use mysqli;

class EmployeeRepository {
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

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

    public function findAll(): array
    {
        $result = $this->db->query("SELECT * FROM employee");

        if (!$result) {
            throw new \RuntimeException("Database error: " . $this->db->error);
        }

        $employees = [];

        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    
        return $employees;
    }
}