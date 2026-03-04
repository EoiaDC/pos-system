<?php
namespace App\Core;

use App\Auth\Auth;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable $handler, array $options = []): void
    {
        $this->routes['GET'][$path] = ['handler' => $handler, 'options' => $options];
    }

    public function post(string $path, callable $handler, array $options = []): void
    {
        $this->routes['POST'][$path] = ['handler' => $handler, 'options' => $options];
    }

    public function dispatch(string $method, string $uri): string
    {
        $method = strtoupper($method);
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            return "<h1>404 Not Found</h1>";
        }

        $route = $this->routes[$method][$path];
        $handler = $route['handler'];
        $options = $route['options'] ?? [];

        // Apply auth guard
        if (!empty($options['auth'])) {
            if (!\Auth::check()) {
                header('Location: ' . APP_BASE_PATH . '/login');
                exit;
            }
        }

        // Apply permission guard
        if (!empty($options['perm'])) {
            \Auth::requirePermission($options['perm']);
        }

        // Call handler
        return (string) call_user_func($handler);
    }
}