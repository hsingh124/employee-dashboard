<?php

namespace App\Repository;

use App\Database\DatabaseInterface;

class CompanyRepository {
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getAverageSalaries(): array
    {
        $query = "
            SELECT company_name, AVG(salary) AS average_salary
            FROM employee
            GROUP BY company_name
            ORDER BY company_name ASC
        ";

        $result = $this->db->query($query);

        if (!$result) {
            throw new \RuntimeException("Query failed: " . $this->db->getError());
        }

        $averages = [];

        while ($row = $result->fetch_assoc()) {
            $averages[] = [
                'company_name' => $row['company_name'],
                'average_salary' => round((float)$row['average_salary'], 2)
            ];
        }

        return $averages;
    }
}