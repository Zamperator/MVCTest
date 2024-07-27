<?php

namespace App\Lib;

use App\Lib\Exceptions\ResourceNotFound;

final class Router
{
    protected array $routes = [];
    protected array $middleware = [];

    public function get(string $route, array $callback): Route
    {
        return $this->addRoute(__FUNCTION__, $route, $callback);
    }

    public function post(string $route, array $callback): Route
    {
        return $this->addRoute(__FUNCTION__, $route, $callback);
    }

    public function put(string $route, array $callback): Route
    {
        return $this->addRoute(__FUNCTION__, $route, $callback);
    }

    public function delete(string $route, array $callback): Route
    {
        return $this->addRoute(__FUNCTION__, $route, $callback);
    }

    private function addRoute(string $method, string $route, array $callback): Route
    {
        $handler = new Route($callback);
        $route = $this->convertToRegex($route);
        $this->routes[strtoupper($method)][$route] = $handler;

        return $handler;
    }

    public function middleware($middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return void
     */
    public function notFound(): void
    {
        ErrorHandler::exception(new ResourceNotFound('Route not found.'));
    }

    private function convertToRegex(string $route): string
    {
        $route = preg_replace_callback('#\{([a-zA-Z_-][a-zA-Z0-9_-]*)(?::(int|alpha|alnum))?}#', function ($matches) {
            $param = $matches[1];
            $type = $matches[2] ?? 'alnum';

            switch ($type) {
                case 'int':
                    return '(?P<' . preg_quote($param) . '>\d+)';
                case 'alpha':
                    return '(?P<' . preg_quote($param) . '>[a-zA-Z]+)';
                case 'alnum':
                default:
                    return '(?P<' . preg_quote($param) . '>[\w-]+)';
            }
        }, $route);

        return '#^' . str_replace('/', '\/', $route) . '\/?$#';
    }

    /**
     * @return void
     * @throws ResourceNotFound
     */
    public function dispatch(): void
    {
        $response = new Response(); // Response-Objekt

        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $uri = '/' . trim($uri, '/');

        $request = $_SERVER; // Einfaches Request-Objekt

        foreach ($this->routes[$method] as $route => $handler) {
            if (preg_match($route, $uri, $matches)) {
                array_shift($matches); // Entfernt das vollständige Match

                // Extrahiere benannte Matches
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                        $_GET[$key] = $value;
                    }
                }

                $callback = $handler->callback;

                // Erstelle Middleware-Pipeline
                $middlewares = array_merge($this->middleware, $handler->middleware);
                $middlewarePipeline = $this->createMiddlewarePipeline($middlewares, function($request, $response) use ($callback, $params) {
                    if (is_callable($callback)) {
                        $controller = new $callback[0]();
                        $method = $callback[1];
                        return call_user_func_array([$controller, $method], $params);
                    } else {
                        $this->notFound();
                        return null; // Exception werfen, falls die Route nicht gefunden wurde
                    }
                });

                // Middleware-Pipeline durchlaufen
                $response = $middlewarePipeline($request, $response);

                // Antwort senden
                if( method_exists($response, 'send')) {
                    $response->send();
                }
                return; // Ausführung abbrechen, sobald eine Route gefunden wurde
            }
        }

        $this->notFound();
    }

    protected function createMiddlewarePipeline($middlewares, $controllerCallback)
    {
        $next = $controllerCallback;
        while ($middleware = array_pop($middlewares)) {
            $next = function($request, $response) use ($middleware, $next) {
                $middlewareInstance = new $middleware();
                return $middlewareInstance->handle($request, $response, $next);
            };
        }
        return $next;
    }
}
