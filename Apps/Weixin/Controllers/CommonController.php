<?php

namespace Apps\Weixin\Controllers;

use Common\Weixin\WxConstants;
use Apps\Models\AutoReplyModel;

abstract class CommonController extends IndexController
{
    abstract protected function getActionController();

    abstract protected function handleText(array $user_info, $postObj);

    abstract protected function handleImage(array $user_info, $postObj);

    abstract protected function handleVoice(array $user_info, $postObj);

    public function reply(array $user_info, $postObj)
    {
        $msg_type = trim($postObj->MsgType);
        switch ($msg_type) {
            case WxConstants::MSGTYPE_TEXT:
                $result = $this->handleText($user_info, $postObj);
                break;
            case WxConstants::MSGTYPE_VOICE:
                $result = $this->handleVoice($user_info, $postObj);
                break;
            case WxConstants::MSGTYPE_IMAGE:
                $result = $this->handleImage($user_info, $postObj);
                break;
            case WxConstants::MSGTYPE_EVENT:
                $result = $this->handleEvent($user_info, $postObj);
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

    protected function autoReply($where, $user_info)
    {
        $autoreply = new AutoReplyModel();
        $result = $autoreply->get($where);
        if ($result) {
            $result = $result[0];
            if ($result['type'] == 'action') {
                $action = $this->getActionController();
                $result = $action->doAction(['action' => $result['content'], 'argument' => $user_info]);
            }
        } else {
            $result = null;
        }
        return $result;
    }

    //处理事件消息
    protected function handleEvent($user_info, $postObj)
    {
        $method = strtolower(trim($postObj->Event));
        return method_exists($this, $method) ? $this->$method($user_info, $postObj) : '';
    }

    //关注事件
    protected function subscribe(array $user_info, $postObj)
    {
        if ($user_info['subscribe'] == 0) {
            $this->user->set(['subcribe' => 1, 'subscribe_time' => intval($postObj->CreateTime)], ['userid' => $user_info['userid']]);
        }
        return $this->autoReply(array('msg_type' => WxConstants::MSGTYPE_EVENT, 'keyword' => WxConstants::EVENT_SUBSCRIBE), $user_info);
    }

    //取消关注事件
    protected function unsubscribe(array $user_info, $postObj)
    {
        return $this->user->set(['subcribe' => 0, 'unsubscribe_time' => intval($postObj->CreateTime)], ['userid' => $user_info['userid']]);
    }

    //点击菜单事件
    protected function click(array $user_info, $postObj)
    {
        $method = strtolower(trim($postObj->EventKey));
        $action = $this->getActionController();
        return $action->doAction(['action' => $method, 'argument' => $user_info]);
    }
}