<?php

namespace App\Controller;

abstract class BaseController
{
    protected string $requestMethod;
    protected string $requestUri;

    public function __construct(string $requestMethod, string $requestUri)
    {
        $this->requestMethod = $requestMethod;
        $this->requestUri = rtrim($requestUri, '/');
    }

    public function dispatch(array $routes): void 
    {   
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
}