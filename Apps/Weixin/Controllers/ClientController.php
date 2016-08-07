<?php

namespace Apps\Weixin\Controllers;

use Common\Libs\Functions;
use Common\Weixin\WxConstants;
use Apps\Models\WaiterModel;

class ClientController extends CommonController
{
    protected function getActionController(){
        return new ActionController();
    }

    protected function getWaiter(array $user_info)
    {
        if (empty($waiter_userid)) {
            if (empty($user_info['waiterid'])) {
                $waiter = new WaiterModel();
                $waiter_userid = $waiter->getNewWaiter();
                $this->user->set(['waiterid' => $waiter_userid], ['userid' => $user_info['userid']]);
            } else {
                $waiter_userid = $user_info['waiterid'];
            }
        }
        return $waiter_userid;
    }

    protected function handleImage(array $user_info, $postObj)
    {
        $waiter_userid = $this->getWaiter($user_info);
        $data = $user_info['userid'] . '号用户' . $user_info['nickname'] . '（' . $user_info['current_city'] . '）：';
        $this->user->sendMessage($waiter_userid, WxConstants::MSGTYPE_TEXT, $data);
        $data = trim($postObj->MediaId);
        $this->user->sendMessage($waiter_userid, WxConstants::MSGTYPE_IMAGE, $data);
        return '';
    }

    protected function handleText(array $user_info, $postObj)
    {
        $waiter_userid = $this->getWaiter($user_info);
        $content = trim($postObj->Content);
        $result = $this->autoReply(array('msg_type' => WxConstants::MSGTYPE_TEXT, 'keyword' => Functions::clearPunctuation($content)),$user_info);
        if (empty($result)) {
            $data = $user_info['userid'] . '号用户' . $user_info['nickname'] . '（' . $user_info['current_city'] . '）：' . $content;
            $this->user->sendMessage($waiter_userid, WxConstants::MSGTYPE_TEXT, $data);
            $result = '';
        }
        return $result;
    }

    protected function handleVoice(array $user_info, $postObj)
    {
        $waiter_userid = $this->getWaiter($user_info);
        $data = $user_info['userid'] . '号用户' . $user_info['nickname'] . '（' . $user_info['current_city'] . '）：';
        $this->user->sendMessage($waiter_userid, WxConstants::MSGTYPE_TEXT, $data);
        $data = trim($postObj->MediaId);
        $this->user->sendMessage($waiter_userid, WxConstants::MSGTYPE_VOICE, $data);
        return '';
    }
}