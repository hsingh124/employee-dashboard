<?php

namespace App\Controller;

use App\Database\DatabaseInterface;
use App\Repository\CompanyRepository;

/**
 * CompanyController handles all company-related routes and actions.
 */
class CompanyController extends BaseController {
    private CompanyRepository $repository;

    public function __construct(DatabaseInterface $db, string $requestMethod, string $requestUri)
    {
        parent::__construct($requestMethod, $requestUri);
        $this->repository = new CompanyRepository($db);
    }

    /**
     * Dispatches company-related routes.
     * 
     * @param array $routes
     * 
     * @return void
     */
    public function dispatch(array $routes = []): void 
    {
        if (empty($routes)) {
            $routes = [
                'GET /companies/average-salaries' => 'getAverageSalaries',
            ];
        }

        parent::dispatch($routes);
    }

    /**
     * Handler for fetching average salaries per company.
     * 
     * @return array JSON response with company salary averages.
     */
    protected function getAverageSalaries(): array
    {
        $salaries = $this->repository->getAverageSalaries();

        return [
            'status' => 200,
            'body' => $salaries
        ];
    }
}