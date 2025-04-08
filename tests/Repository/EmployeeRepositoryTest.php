<?php

use App\Database\DatabaseInterface;
use App\Repository\EmployeeRepository;
use PHPUnit\Framework\TestCase;

class EmployeeRepositoryTest extends TestCase
{
    public function testFindAllReturnsEmployees(): void
    {
        $mockDb = $this->createMock(DatabaseInterface::class);

        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'company_name' => 'ACME Corporation', 'employee_name' => 'John Doe', 'email_address' => 'johndoe@acme.com', 'salary' => 50000],
                ['id' => 2, 'company_name' => 'Stark Industries', 'employee_name' => 'Tony Stark', 'email_address' => 'tony@stark.com', 'salary' => 100000],
                null
            );

        $mockDb->method('query')
            ->willReturn($mockResult);

        $repo = new EmployeeRepository($mockDb);
        $employees = $repo->findAll();

        $this->assertCount(2, $employees);
        $this->assertEquals('John Doe', $employees[0]['employee_name']);
        $this->assertEquals('Tony Stark', $employees[1]['employee_name']);
    }
}
