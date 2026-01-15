<?php
require_once dirname(__DIR__) . '/core/bootstrap.php';
$router = new Router();
$router->dispatch($_SERVER["REQUEST_URI"]);

date_default_timezone_set('Asia/Ho_Chi_Minh');
