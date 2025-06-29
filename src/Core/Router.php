<?php

namespace App\Core;

class Router
{
    private string $url;
    private string $server;

    /**
     * @var Route[]
     */
    private array $routes = [];

    /**
     * @var Route[]
     */
    private array $namedRoutes = [];

    public function __construct()
    {
        $this->url = $_GET['url'] ?? parse_url($_SERVER['REQUEST_URI'])['path'] ?? '/';
        $this->server = $_SERVER['SERVER_PORT'] !== 80 
            ? sprintf("%s:%s", $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'])
            : sprintf("%s", $_SERVER['SERVER_NAME']);

        // trailing slash
        if (strlen($this->url) > 1 && $this->url[-1] === '/') {
            $url = substr($this->url, 0, -1);
            header("Location: /{$url}", true, 301);
        }
    }

    /**
     * register a route
     */
    private function add(string $path, array $controller, ?string $name, string $method): Route
    {
        $route = new Route($path, $controller);
        $this->routes[$method][] = $route;

        if (!is_null($name)) {
            $this->namedRoutes[$name] = $route;
        }

        return $route;
    }

    /**
     * register a route for http method
     */
    public function any(string $path, array $controller, ?string $name = null): Route
    {
        $route = new Route($path, $controller);
        $this->routes['GET'][] = $route;
        $this->routes['POST'][] = $route;

        if (!is_null($name)) {
            $this->namedRoutes[$name] = $route;
        }
        return $route;
    }

    /**
     * register a route for GET http method
     */
    public function get(string $path, array $controller, ?string $name = null): Route
    {
        return $this->add($path, $controller, $name, "GET");
    }

    /**
     * register a route for POST http method
     */
    public function post(string $path, array $controller, ?string $name = null): Route
    {
        return $this->add($path, $controller, $name, "POST");
    }

    public function url(string $name, array $params = []): mixed
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception(sprintf("No matched routes for : %s", $name));
        }
        return $this->namedRoutes[$name]->generateUri($params);
    }

    public function run(): ?Route
    {
        if (isset($_SERVER['REQUEST_METHOD']) && isset($this->routes[$_SERVER['REQUEST_METHOD']])) {

            /** @var Route $route */
            foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
                if ($route->match($this->url)) {
                    return $route;
                }
            }
            return null;
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function generateUri(string $name, array $params = []): string
    {  
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("route with name $name is not registered");
        }

        $route = $this->namedRoutes[$name];
        return sprintf("http://%s/%s", $this->server, $route->generateUri($params));
    }

    public function redirect(string $name, array $params = []): void
    {
        header("Location: {$this->generateUri($name, $params)}", true, 301);
        exit();
    }
}
