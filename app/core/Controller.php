<?php

use Jenssegers\Blade\Blade;

class Controller
{
    public function view(string $view, array $data = []): void
    {
        $views = VIEW_PATH;
        $cache = BASE_PATH . '/storage/cache';

        if (!file_exists($cache)) {
            mkdir($cache, 0777, true);
        }

        $blade = new Blade($views, $cache);

        echo $blade->render($view, $data);
    }


    protected function normalizeViewName(string $view): string
    {
        $view = trim($view);
        $view = str_replace(['\\', '/'], '.', $view);
        $view = preg_replace('/\.+/', '.', $view);
        return trim($view, '.');
    }
    // product ->
    public function model($name)
    {
        $class = ucfirst($name);
        if (!class_exists($class)) {
            throw new Exception("class not found");
        }
        return new $class();
    }

    public function redirect($path)
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        $target = $base . '/' . ltrim($path, '/');
        header('Location: ' . $target);
        exit;
    }

    public function notFound($message): void
    {
        http_response_code(404);
        /**
         * sau nay co the load theo view errors
         */
        echo "controller Not Found - ' . $message. </h1>";
    }
}
