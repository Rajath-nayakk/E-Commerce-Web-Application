<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Class Router
 * 
 * Elegant, lightweight routing engine with parameterized dynamic routes
 * (e.g. /products/{slug}), middleware pipelines, and RESTful HTTP verb handling.
 */
class Router
{
    private array $routes = [];
    private array $groupMiddleware = [];

    public function get(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, array|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function group(array $attributes, callable $callback): void
    {
        $previousMiddleware = $this->groupMiddleware;
        if (isset($attributes['middleware'])) {
            $middlewares = is_array($attributes['middleware']) ? $attributes['middleware'] : [$attributes['middleware']];
            $this->groupMiddleware = array_merge($this->groupMiddleware, $middlewares);
        }

        $callback($this);

        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middleware = []): self
    {
        $allMiddleware = array_merge($this->groupMiddleware, $middleware);
        $cleanPath = '/' . trim($path, '/');
        if ($cleanPath === '//') {
            $cleanPath = '/';
        }

        // Convert {param} placeholders into regex captures: (?P<param>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $cleanPath);
        $pattern = "#^" . $pattern . "$#";

        $this->routes[] = [
            'method'     => strtoupper($method),
            'path'       => $cleanPath,
            'pattern'    => $pattern,
            'handler'    => $handler,
            'middleware' => $allMiddleware
        ];

        return $this;
    }

    /**
     * Dispatch incoming request to matching route handler
     */
    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named capture parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }

                // Execute middleware pipeline
                $this->runMiddleware($route['middleware'], $request);

                // Execute handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func($handler, $request, ...array_values($params));
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $action] = $handler;
                    if (!class_exists($controllerClass)) {
                        throw new \Exception("Controller class {$controllerClass} not found");
                    }
                    $controller = new $controllerClass();
                    if (!method_exists($controller, $action)) {
                        throw new \Exception("Action {$action} not found in {$controllerClass}");
                    }
                    $controller->$action($request, ...array_values($params));
                    return;
                }
            }
        }

        // No route matched: 404 Not Found
        if ($request->isJson() || str_starts_with($uri, '/api')) {
            Response::error('Resource not found', 404);
            return;
        }

        Response::status(404);
        View::render('errors/404', ['title' => 'Page Not Found']);
    }

    /**
     * Run middleware checks before invoking route handler
     */
    private function runMiddleware(array $middlewares, Request $request): void
    {
        foreach ($middlewares as $mw) {
            match ($mw) {
                'auth' => Auth::requireLogin($request),
                'admin' => Auth::requireAdmin($request),
                'guest' => Auth::requireGuest($request),
                default => null
            };
        }
    }
}
