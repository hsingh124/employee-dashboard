<?php

namespace App\Controller;

/**
 * BaseController provides routing and response handling
 * logic for controllers that extend it.
 */
abstract class BaseController
{
    protected string $requestMethod;
    protected string $requestUri;

    public function __construct(string $requestMethod, string $requestUri)
    {
        $this->requestMethod = $requestMethod;
        $this->requestUri = rtrim($requestUri, '/');
    }

    /**
     * Dispatch the request by matching routes to handlers.
     * If no route matches, sends a 404 response.
     * 
     * @param array $routes
     * 
     * @return void
     */
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

    /**
     * Checks if the current request matches a given route and handles it.
     * 
     * @param string $routeMethod
     * @param string $routePattern
     * @param string $handlerMethod
     * 
     * @return bool
     */
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

    /**
     * Match the URI against a route pattern (supports {id} placeholder).
     * 
     * @param string $pattern
     * 
     * @return bool
     */
    private function match(string $pattern): bool
    {
        $regex = preg_replace('#\{id\}#', '(\d+)', $pattern);
        return preg_match("#^$regex$#", $this->requestUri);
    }


    /**
     * Ensures the route handler method exists in the subclass.
     * 
     * @param string $method
     * 
     * @return void
     */
    private function ensureMethodExists(string $method): void
    {
        if (!method_exists($this, $method)) {
            throw new \RuntimeException("Method {$method} does not exist in " . static::class);
        }
    }

    /**
     * Sends a JSON HTTP response with the given status and body.
     * 
     * @param array $response
     * 
     * @return void
     */
    private function sendResponse(array $response): void
    {
        $status = $response['status'] ?? 200;
        $body = $response['body'] ?? null;

        if ($body !== null) {
            echo json_encode($body);
        }
    }
}