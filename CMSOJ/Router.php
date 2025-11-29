<?php

namespace CMSOJ;

class Router
{
    protected array $routes = [];

    public function get(string $pattern, $callback): void
    {
        $this->addRoute('GET', $pattern, $callback);
    }

    private function addRoute(string $method, string $pattern, $callback): void
    {
        // Normalize: remove leading/trailing slashes
        $pattern = trim($pattern, '/');
        $this->routes[$method][$pattern] = $callback;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach ($this->routes[$method] ?? [] as $pattern => $callback) {

            // Convert {param} → regex
            $regex = preg_replace('/\{([^\/]+)\}/', '([^/]+)', $pattern);

            if (preg_match("#^$regex$#", $uri, $matches)) {

                array_shift($matches); // remove full match

                // -------------------------------------------------------------
                //  A) CONTROLLER SYNTAX: [ClassName::class, 'method']
                // -------------------------------------------------------------
                if (is_array($callback) && count($callback) === 2) {

                    [$class, $method] = $callback;

                    // Instantiate controller automatically if needed
                    if (is_string($class)) {
                        $class = new $class();
                    }

                    return call_user_func_array([$class, $method], $matches);
                }

                // -------------------------------------------------------------
                //  B) Closure route: function (...) { }
                // -------------------------------------------------------------
                return call_user_func_array($callback, $matches);
            }
        }

        // -------------------------------------------------------------
        // 404 fallback
        // -------------------------------------------------------------
        http_response_code(404);
        \CMSOJ\Template::view('CMSOJ/Views/404.html');
        exit;
    }
}
