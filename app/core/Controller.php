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

    public function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");

            echo '<!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>404 Not Found</title>
                <style>
                    body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; text-align: center; padding-top: 10%; background: #f8f9fa; color: #333; }
                    h1 { font-size: 80px; margin-bottom: 0; color: #dc3545; }
                    p { font-size: 18px; color: #666; margin-top: 10px; }
                    a { display: inline-block; margin-top: 20px; padding: 10px 25px; background: #111; color: #fff; text-decoration: none; border-radius: 5px; transition: 0.3s; }
                    a:hover { background: #333; }
                </style>
            </head>
            <body>
                <h1>404</h1>
                <h2>Không tìm thấy trang</h2>
                <p>Opps! Trang bạn yêu cầu không tồn tại hoặc bạn không có quyền truy cập khu vực này.</p>
                <a href="/home">Quay lại Trang Chủ</a>
            </body>
            </html>';

            exit;
        }
    }

    public function notFound($message): void
    {
        http_response_code(404);
        /**
         * sau nay co the load theo view errors
         */
        // Đã sửa lại lỗi nháy đơn/nháy kép ở dòng này
        echo "<h1>Controller Not Found - " . htmlspecialchars($message) . "</h1>";
    }
}
