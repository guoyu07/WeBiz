<?php

namespace Apps\Weixin\Controllers;

use Apps\Models\ActionModel;
use Apps\Models\MenuModel;
use Common\Weixin\WxConstants;

class AdminActionController extends WaiterActionController
{
    //创建个性化自定义菜单
    protected function createmenu($menuid = null)
    {
        $menu = new MenuModel();
        $feedback = $menu->createMenu($menuid);
        return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $feedback['errmsg']);
    }

    //删除自定义菜单
    protected function delmenu($menuid = null)
    {
        $menu = new MenuModel();
        $feedback = $menu->delMenu($menuid);
        return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $feedback['errmsg']);
    }

    //清空redis
    protected function flushredis()
    {
        $action = new ActionModel();
        $content = $action->flushredis() ? '已成功清空redis' : '未能成功清空redis';
        return ['type' => WxConstants::MSGTYPE_TEXT, 'content' => $content];
    }

    //清空Twig缓存
    protected function refresh($dir = null)
    {
        $action = new ActionModel();
        $content = $action->refresh($dir) ? '已成功清空Twig缓存' : '未能成功清空Twig缓存';
        return ['type' => WxConstants::MSGTYPE_TEXT, 'content' => $content];
    }
}