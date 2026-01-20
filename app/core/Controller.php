<?php

use Jenssegers\Blade\Blade;

class Controller
{
    public function view(string $view, array $data = []): void
    {
        // Chuẩn hoá về dạng dot: "home/index" -> "home.index"
        $normalizedView = $this->normalizeViewName($view);

        // Map sang đường dẫn file để kiểm tra tồn tại
        $viewPath = str_replace('.', '/', $normalizedView);

        $candidates = [
            VIEW_PATH . '/' . $viewPath . '.blade.php',
            VIEW_PATH . '/' . $viewPath . '.blade', // nếu bạn thật sự có kiểu file này
        ];

        $found = null;
        foreach ($candidates as $file) {
            if (is_file($file)) {
                $found = $file;
                break;
            }
        }

        if (!$found) {
            throw new RuntimeException("Blade view not found: {$view} (resolved: {$viewPath})");
        }

        $cachePath = BASE_PATH . '/storage/cache';
        if (!is_dir($cachePath) && !mkdir($cachePath, 0775, true) && !is_dir($cachePath)) {
            throw new RuntimeException("Cannot create cache directory: {$cachePath}");
        }

        // Quan trọng: truyền THƯ MỤC views, không truyền $viewPath
        $blade = new Blade(VIEW_PATH, $cachePath);

        echo $blade->render($normalizedView, $data);
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