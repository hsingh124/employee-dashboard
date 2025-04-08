<?php

namespace App\Controller;

use App\Database\DatabaseInterface;
use App\Repository\CompanyRepository;

class CompanyController extends BaseController {
    private CompanyRepository $repository;

    public function __construct(DatabaseInterface $db, string $requestMethod, string $requestUri)
    {
        parent::__construct($requestMethod, $requestUri);
        $this->repository = new CompanyRepository($db);
    }

    public function dispatch(array $routes = []): void 
    {
        if (empty($routes)) {
            $routes = [
                'GET /companies/average-salaries' => 'getAverageSalaries',
            ];
        }

        parent::dispatch($routes);
    }

    protected function getAverageSalaries(): array
    {
        $salaries = $this->repository->getAverageSalaries();

        return [
            'status' => 200,
            'body' => $salaries
        ];
    }
}