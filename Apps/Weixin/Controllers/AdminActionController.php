<?php

namespace Apps\Weixin\Controllers;

use Apps\Models\MenuModel;
use Common\Libs\Functions;
use Common\Weixin\WxConstants;

class AdminActionController extends WaiterActionController
{
    //创建个性化自定义菜单
    protected function createmenu($message = null)
    {
        $menu = new MenuModel();
        $menu = $menu->get($message);
        $feedback = $this->user->weixin->createMenu($menu);
        return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $feedback['errmsg']);
    }

    //删除自定义菜单
    protected function delmenu($menuid = null)
    {
        $menu = new MenuModel();
        $feedback = $menu->del($menuid);
        return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $feedback['errmsg']);
    }

    //清空redis
    protected function flushredis()
    {
        $result = array('type' => WxConstants::MSGTYPE_TEXT, 'content' => '');
        $this->user->redis->flushdb() ? $result['content'] = '已成功清空redis' : $result['content'] = '未能成功清空redis';
        return $result;
    }

    //清空Twig缓存
    protected function refresh($dir = null)
    {
        if (empty($dir)) $dir = ROOT . 'Apps' . DIRECTORY_SEPARATOR . 'Html' . DIRECTORY_SEPARATOR . 'Cache';
        Functions::delAllFiles($dir);
        return ['type' => WxConstants::MSGTYPE_TEXT, 'content' => '已成功清空Twig缓存'];
    }
}