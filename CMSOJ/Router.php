<?php

class Router
{
    protected $routes = [];

    public function get($pattern, $callback)
    {
        $this->routes['GET'][$pattern] = $callback;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach ($this->routes[$method] ?? [] as $pattern => $callback) {

            // Convert route parameters (/blog/{id})
            $regex = preg_replace('/\{([^\/]+)\}/', '([^/]+)', $pattern);

            if (preg_match('#^' . $regex . '$#', $uri, $matches)) {
                array_shift($matches); // remove full match
                return call_user_func_array($callback, $matches);
            }
        }

        // 404 fallback
        http_response_code(404);
        Template::view('CMSOJ/Views/404.html');
        exit;
    }
}
