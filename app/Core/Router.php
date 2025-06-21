<?php
namespace App\Core;

/**
 * Class Router
 *
 * A simple routing class that maps HTTP routes to controller actions.
 */
class Router
{
    protected $routes = [];

    /**
     * Register a GET route.
     *
     * @param string $uri
     * @param string $controllerAction
     */
    public function get($uri, $controllerAction)
    {
        $this->addRoute('GET', $uri, $controllerAction);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri
     * @param string $controllerAction
     */
    public function post($uri, $controllerAction)
    {
        $this->addRoute('POST', $uri, $controllerAction);
    }

    /**
     * Internal method to store route definition.
     *
     * @param string $method
     * @param string $uri
     * @param string $controllerAction
     */
    protected function addRoute($method, $uri, $controllerAction)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $controllerAction
        ];
    }

    /**
     * Dispatch the current HTTP request to the appropriate controller action.
     */
    public function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Clean up double slashes (e.g. /webapp/public//clients => /webapp/public/clients)
        $requestUri = preg_replace('#/+#', '/', $requestUri);

        // Define the base URI for routing (adjust if your public folder changes)
        $baseUri = '/webapp/public';

        // Remove the base URI prefix from the request URI
        if (strpos($requestUri, $baseUri) === 0) {
            $requestUri = substr($requestUri, strlen($baseUri));
            if ($requestUri === '') {
                $requestUri = '/';
            }
        }

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Search for a matching route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Convert dynamic segments like {id} to regex
            $pattern = preg_replace('#\{[^\}]+\}#', '([^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match

                // Split controller and method
                [$controllerName, $method] = explode('@', $route['action']);
                $controllerClass = 'App\\Controllers\\' . $controllerName;

                // Ensure controller exists
                if (!class_exists($controllerClass)) {
                    header("HTTP/1.0 404 Not Found");
                    echo "Controller $controllerClass not found";
                    exit;
                }

                $controller = new $controllerClass();

                // Ensure method exists in controller
                if (!method_exists($controller, $method)) {
                    header("HTTP/1.0 404 Not Found");
                    echo "Method $method not found in controller $controllerClass";
                    exit;
                }

                // Call the controller method with matched parameters
                call_user_func_array([$controller, $method], $matches);
                return;
            }
        }

        // No route matched
        header("HTTP/1.0 404 Not Found");
        echo "Not Found";
    }
}
