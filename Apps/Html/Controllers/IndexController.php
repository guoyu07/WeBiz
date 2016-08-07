<?php

namespace Apps\Html\Controllers;

class IndexController
{
    public function start()
    {
        define('CACHE_DIR', ROOT . 'Apps' . DIRECTORY_SEPARATOR . 'Html' . DIRECTORY_SEPARATOR . 'Cache');//定义页面缓存目录
        define('WXPAY_LOG_DIR', LOG_DIR . DIRECTORY_SEPARATOR . 'WxPay');//定义微信支付日志目录
        spl_autoload_register(array(__CLASS__, 'loadTwig'));
        $controller = $this->router();
        $controller->start();
    }

    protected function router()
    {
        if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $controller = new NotifyController(); //微信支付回调
        } elseif (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
            $controller = new UserAgentController(); //非微信浏览器跳转错误
        } elseif (!empty($_GET['c'])) { //若通过get指定控制器则跳转到指定控制器
            $controller_name = __NAMESPACE__ . '\\' . ucwords($_GET['c']) . 'Controller';
            $controller = new $controller_name();
        } elseif (!empty($_GET['keyword'])) { //若获取查找结果则跳转到查找结果控制器
            $controller = new QueryController();
        } else {
            $controller = new HomeController(); //未指定控制器时返回主页
        }
        return $controller;
    }

    protected static function loadTwig($class)
    {
        if (0 !== strpos($class, 'Twig')) {
            return;
        }

        if (is_file($file = ROOT . 'Common' . DIRECTORY_SEPARATOR . str_replace(array('_', "\0"), array('/', ''), $class) . '.php')) {
            require $file;
        }
    }
}