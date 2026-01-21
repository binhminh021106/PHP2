<?php

use Jenssegers\Blade\Blade;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH);
define('VIEW_PATH', APP_PATH . '/views');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');
define('CACHE_PATH', APP_PATH . '/storage/cache');

$vendorAutoload = BASE_PATH . "/vendor/autoload.php";

// var_dump("Đường dẫn Vendor: " . $vendorAutoload);

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    die("Lỗi: Không tìm thấy file vendor/autoload.php tại: " . $vendorAutoload . ". <br>Bạn đã chạy lệnh 'composer install' trong thư mục app chưa?");
}

if (class_exists(\Dotenv\Dotenv::class)) {
    try {
        $dotenv = \Dotenv\Dotenv::createImmutable(BASE_PATH);
        $dotenv->load();
    } catch (Exception $e) {
        echo "Chưa có file .env hoặc lỗi: " . $e->getMessage();
    }
}

// Cấu hình Blade
if (class_exists(Blade::class)) {
    if (!file_exists(CACHE_PATH)) {
        mkdir(CACHE_PATH, 0777, true);
    }

    $blade = new Blade(VIEW_PATH, CACHE_PATH);

    if (!function_exists('view')) {
        function view($view, $data = [])
        {
            global $blade;
            echo $blade->make($view, $data)->render();
        }
    }
}

spl_autoload_register(function (string $class): void {
    $paths = [
        APP_PATH . '/core/' . $class . '.php',
        CONTROLLER_PATH . '/' . $class . '.php',
        MODEL_PATH . '/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
