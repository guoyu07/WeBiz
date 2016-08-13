<?php

namespace Apps\Html\Controllers;

use Common\Weixin\WxConstants;
use Apps\Models\ExpertModel;
use Apps\Models\UserModel;

class ActionController
{
    protected $user;

    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function start()
    {
        $expert = new ExpertModel();
        $expert_info = $expert->get(array('id' => $this->getExpertId()))[0];
        $client_userid = $this->getClientUserid();
        $time = $this->getExpire();
        if ($this->bind($client_userid, $expert_info['userid'], $time)) {
            $time = $time / 60;
            $expert_content = '您好，客户' . $client_userid . '已经付费预约了您的服务，您可以直接在微信号里发送文字、图片和语音与客户交流。时间期限为' . $time . '分钟。';
            $this->user->sendMessage($expert_info['userid'], WxConstants::MSGTYPE_TEXT, $expert_content);
            $content = '您好，已为您成功预约专家【' . $expert_info['name'] . '】' . $expert_info['title'] . '，您可以直接在微信号里发送文字、图片和语音与专家交流。时间期限为' . $time . '分钟。';
        } else {
            $content = '非常抱歉，未能成功预约专家【' . $expert_info['name'] . '】' . $expert_info['title'] . '，您支付的钱款我们将在24小时之内退还给您。';
        }
        $this->user->sendMessage($client_userid, WxConstants::MSGTYPE_TEXT, $content);
    }

    protected function bind($client, $expert, $expire)
    {
        return $this->user->bind($client, $expert, $expire);
    }

    protected function getExpertId()
    {
        return $_GET['expert'];
    }

    protected function getClientUserid()
    {
        return $_GET['client'];
    }

    protected function getExpire()
    {
        return $_GET['time'];
    }
}