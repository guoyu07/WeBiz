<?php

namespace Apps\Weixin\Controllers;

use Common\Weixin\WxConstants;

class WaiterActionController extends ActionController
{
    protected function sendtext($data)
    {
        $result = $this->user->sendMessage($data['touserid'], WxConstants::MSGTYPE_TEXT, $data['content']);
        if ($result) {
            $result['errcode'] == 0 ? $result = '' : $result = array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $result['errmsg']);
        } else {
            $result = array('type' => WxConstants::MSGTYPE_TEXT, 'content' => '找不到您输入的用户，请核实用户id');
        }
        return $result;
    }

    protected function sendimage($data)
    {
        $result = $this->user->sendMessage($data['touserid'], WxConstants::MSGTYPE_IMAGE, $data['mediaid']);
        if ($result) {
            $result['errcode'] == 0 ? $result = '' : $result = array('type' => WxConstants::MSGTYPE_IMAGE, 'content' => $result['errmsg']);
        } else {
            $result = array('type' => WxConstants::MSGTYPE_TEXT, 'content' => '找不到您输入的用户，请核实用户id');
        }
        return $result;
    }

    protected function sendvoice($data)
    {
        $result = $this->user->sendMessage($data['touserid'], WxConstants::MSGTYPE_VOICE, $data['mediaid']);
        if ($result) {
            $result['errcode'] == 0 ? $result = '' : $result = array('type' => WxConstants::MSGTYPE_VOICE, 'content' => $result['errmsg']);
        } else {
            $result = array('type' => WxConstants::MSGTYPE_TEXT, 'content' => '找不到您输入的用户，请核实用户id');
        }
        return $result;
    }

    //查看正在服务的客户
    protected function getcustomer($data)
    {
        $user_info = $this->user->get(array('userid' => intval($data)));
        if ($user_info) {
            $content = '用户ID: ' . $user_info['userid'] . "\n";
            $content .= '昵称: ' . $user_info['nickname'] . "\n";
            $content .= '性别: ' . $user_info['sex'];
        } else {
            $content = '找不到您输入的用户，请核实用户id';
        }
        return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $content);
    }
}