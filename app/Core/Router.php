<?php

class Router
{
    public function dispatch(string $uri): void
    {

        $path = parse_url($uri, PHP_URL_PATH) ?? '';
        $path = trim($path, '/');

        $basePath = $this->basePath();
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = trim(substr($path, strlen($basePath)), '/');
        }

        $segments = $path === '' ? [] : explode('/', $path);
        $controllerName = ucfirst($segments[0] ?? 'home') . 'Controller';
        $action = $segments[1] ?? 'index';
        $params = array_slice($segments, 2);

        if (!class_exists($controllerName)) {
            $this->notFound('Class not exist');
            return;
        }

        $controller = new $controllerName;
        if (!method_exists($controller, $action)) {
            $this->notFound('Class not exist');
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }

    public function basePath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = trim(dirname($scriptName), '/');
        return $dir;
    }

    public function notFound($message): void
    {
        http_response_code(404);
        echo "<h1> 404 Not Found - " . $message . "</h1>";
    }
}
