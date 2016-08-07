<?php

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);//定义根目录
define('LOG_DIR', ROOT . 'Assets' . DIRECTORY_SEPARATOR . 'logs');//定义日志目录

ini_set('date.timezone', 'Asia/Shanghai');
ini_set('error_log', LOG_DIR . DIRECTORY_SEPARATOR . 'php_errors.log');

require_once 'Apps' . DIRECTORY_SEPARATOR . 'Router.php';
Router::start();