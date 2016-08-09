<?php

namespace Apps;

use Config\AppConfig;
use Apps\Weixin\Controllers\IndexController as Weixin;
use Apps\Html\Controllers\IndexController as Html;

class WeBiz
{
    public static function start()
    {
        //注册自动加载函数
        spl_autoload_register(array(__CLASS__, 'autoload'));

        //判断是微信访问还是网页访问并启动不同的控制器
        $controller = self::checkSignature(AppConfig::WEIXIN_TOKEN) ? new Weixin() : new Html();
        $controller->start();
    }

    protected static function autoload($class)
    {

        if (0 !== strpos($class, 'Twig')) {
            //转义反斜杠
            if (0 !== strpos('\\', $class)) {
                $class = ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $class);
            }
            //设定类后缀名
            $suffix = array('.class.php', '.php');
            foreach ($suffix as $s) {
                $file = $class . $s;
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        } else {
            if (is_file($file = TWIG_DIR . str_replace(array('_', "\0"), array('/', ''), $class) . '.php')) {
                require $file;
            }
        }
    }

    //检查签名
    private static function checkSignature($token)
    {
        if (!empty($_GET['signature'])) {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if ($tmpStr == $signature) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}