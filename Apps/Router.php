<?php

use Common\Weixin\WxResponse;
use Apps\Weixin\Controllers\IndexController as Weixin;
use Apps\Html\Controllers\IndexController as Html;

class Router
{
    public static function start()
    {
        //注册自动加载函数
        spl_autoload_register(array(__CLASS__, 'autoload'));

        //判断是微信访问还是网页访问并启动不同的控制器
        $controller = WxResponse::check() ? new Weixin() : new Html();
        $controller->start();
    }

    protected static function autoload($class)
    {
        //转义反斜杠
        if (0 !== strpos('\\', $class)) {
            $class = ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $class);
        }

        //设定类后缀名
        $suffix = array('.class.php', '.php', '.inc');
        foreach ($suffix as $s) {
            $file = $class . $s;
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
}