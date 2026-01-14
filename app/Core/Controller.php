<?php

class Controller
{
    public function view($path, $data)
    {
        $viewFile = VIEW_PATH . "/$path" . ".php";
        if (!file_exists($viewFile)) {
            throw new Error("View file not found: " . $viewFile);
        }
        extract($data, EXTR_SKIP);
        require $viewFile;
    }
    public function model($name)
    {
        $class = ucfirst($name);
        if (!class_exists($class)) {
            throw new Error("Model class not found: " . $class);
        }
        return new $class();
    }
    public function redirect($path)
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        $target = $base . '/' . ltrim($path, '/');
        header("Location: $target");
        exit();
    }
    public function notfound($message): void
    {
        http_response_code(404);
        echo "<h1>Controller Not Found - " . htmlspecialchars($message) . "</h1>";
    }
}
