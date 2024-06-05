<?php

namespace App\Lib;

/**
 * @package App\Lib
 * @version 1.0
 * @since 1.0
 */
class Router
{
    protected array $routes = [];
    protected array $middleware = [];

    /**
     * @param string $route
     * @param array $callback
     * @return void
     */
    public function get(string $route, array $callback): void
    {
        // Umgebe den Methodenaufruf mit einer Closure
        $handler = function ($params = []) use ($callback) {
            $controllerClass = $callback[0];
            $method = $callback[1];
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        };

        $route = $this->convertToRegex($route);
        $this->routes['GET'][$route] = $handler;
    }

    /**
     * @param $middleware
     * @return void
     */
    public function middleware($middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return void
     */
    private function notFound(): void
    {
        http_response_code(404);
        exit("404 - Page not found");
    }

    /**
     * Convert route to regex
     * @param string $route
     * @return string
     */
    private function convertToRegex(string $route): string
    {
        $route = preg_replace('#\{[a-zA-Z]+}#', '([a-zA-Z0-9_\-]+)', $route);
        return '#^' . str_replace('/', '\/', $route) . '$#';
    }

    /**
     * @return void
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $uri = '/' . trim($uri, '/');

        foreach ($this->middleware as $middleware) {
            if (is_callable($middleware)) {
                call_user_func($middleware);
            }
        }

        foreach ($this->routes[$method] as $route => $callback) {
            if (preg_match($route, $uri, $matches)) {
                array_shift($matches); // Entfernt das vollständige Match
                if (is_callable($callback)) {
                    call_user_func($callback, $matches);
                } else {
                    $this->notFound();
                }
                return;
            }
        }

        $this->notFound();
    }
}
