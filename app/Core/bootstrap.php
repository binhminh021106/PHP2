<?php

define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');

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