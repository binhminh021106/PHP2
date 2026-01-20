<?php

use Dotenv\Dotenv;

define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');

$vendorAutoload = BASE_PATH . "/app/vendor/autoload.php";
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    echo "not Working";
}

if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(APP_PATH)->safeLoad();
}

// var_dump($_ENV);

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
