<?php

namespace Apps\Html\Controllers;

class IndexController
{
    public function start()
    {
        $controller = $this->router();
        $controller->start();
    }

    protected function router()
    {
        //判断是否为微信支付回调
        if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
            return new NotifyController();
        }

        //判断是否使用了微信浏览器
        if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
            return new UserAgentController();
        }

        //根据指定控制器调用相应控制器
        if (!empty($_GET['c'])) { //若通过get指定控制器则跳转到指定控制器
            $controller_name = __NAMESPACE__ . '\\' . ucwords($_GET['c']) . 'Controller';
            if (class_exists($controller_name)) return new $controller_name();
        }

        //启动搜索查询控制器
        if (!empty($_GET['keyword'])) { //若获取查找结果则跳转到查找结果控制器
            return new QueryController();
        }

        //默认主页控制器
        return new HomeController(); //未指定控制器时返回主页
    }
}