<?php

use PHPUnit\Framework\TestCase;
use App\Repository\CompanyRepository;
use App\Database\DatabaseInterface;

class CompanyRepositoryTest extends TestCase
{
    public function testGetAverageSalaries(): void
    {
        $mockResult = $this->createMock(mysqli_result::class);

        $mockResult->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(
                ['company_name' => 'ACME Corporation', 'average_salary' => 56000],
                ['company_name' => 'Stark Industries', 'average_salary' => 78750],
                ['company_name' => 'Wayne Enterprises', 'average_salary' => 63750],
                null
            );

        $mockDb = $this->createMock(DatabaseInterface::class);
        $mockDb->method('query')
               ->willReturn($mockResult);

        $repo = new CompanyRepository($mockDb);
        $result = $repo->getAverageSalaries();

        $this->assertCount(3, $result);
        $this->assertSame('ACME Corporation', $result[0]['company_name']);
        $this->assertSame(56000.0, $result[0]['average_salary']);
        $this->assertSame('Stark Industries', $result[1]['company_name']);
        $this->assertSame(78750.0, $result[1]['average_salary']);
        $this->assertSame('Wayne Enterprises', $result[2]['company_name']);
        $this->assertSame(63750.0, $result[2]['average_salary']);
    }

    public function testQueryFailureThrowsException(): void
    {
        $mockDb = $this->createMock(DatabaseInterface::class);
        $mockDb->method('query')->willReturn(false);
        $mockDb->method('getError')->willReturn('Mocked DB error');

        $repo = new CompanyRepository($mockDb);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Query failed: Mocked DB error');

        $repo->getAverageSalaries();
    }
}
