<?php

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR); //定义根目录
define('LOG_DIR', ROOT . 'Assets' . DIRECTORY_SEPARATOR . 'logs'); //定义日志目录
define('WXPAY_LOG_DIR', LOG_DIR . DIRECTORY_SEPARATOR . 'WxPay');//定义微信支付日志目录
define('TWIG_DIR', ROOT . 'Common' . DIRECTORY_SEPARATOR . 'Twig'); //定义Twig类所在目录
define('TEMPLATE_DIR', ROOT . 'Apps' . DIRECTORY_SEPARATOR . 'Html' . DIRECTORY_SEPARATOR . 'Views');//定义模板目录
define('CACHE_DIR', ROOT . 'Apps' . DIRECTORY_SEPARATOR . 'Html' . DIRECTORY_SEPARATOR . 'Cache');//定义页面缓存目录
define('CORE', ROOT . 'Apps' . DIRECTORY_SEPARATOR . 'WeBiz.php'); //定义框架入口

require_once CORE;
Apps\WeBiz::start();