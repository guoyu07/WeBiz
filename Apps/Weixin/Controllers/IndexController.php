<?php

namespace Apps\Weixin\Controllers;

use Common\Weixin\WxConstants;
use Common\Weixin\WxResponse;
use Config\AppConfig;
use Apps\Models\RecordModel;
use Apps\Models\UserModel;

class IndexController
{
    protected $user;

    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function start()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (isset($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $result = $this->router($postObj);
            $result = is_array($result) ? WxResponse::response($postObj, $result['content'], $result['type']) : '';
        } else {
            $result = '';
        }
        echo $result;
    }

    private function router($postObj)
    {
        $user_info = $this->getUser(trim($postObj->FromUserName));

        //如果无法获取用户基本信息返回错误
        if (empty($user_info)) {
            $content = '抱歉，暂时无法获取您的用户信息，不能为您提供服务，请稍候再试。';
            return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => $content);
        }

        //存储用户互动记录
        $this->storeRecord($postObj, $user_info['userid']);

        //判断是否有绑定对话对象
        $to_userid = $this->user->getPartner($user_info['userid']);
        if (empty($to_userid) || trim($postObj->MsgType) == WxConstants::MSGTYPE_EVENT || !AppConfig::ALLOW_ONE_ON_ONE) {
            $controller = $this->getController($user_info['groupid']);
            $result = $controller->reply($user_info, $postObj);
        } else {
            $result = $this->user->transfer($postObj, intval($to_userid));
        }
        return $result;
    }

    private function getUser($openid)
    {
        return $this->user->get(['openid' => $openid]);
    }

    private function getController($groupid){
        switch ($groupid) {
            case AppConfig::USER_ADMIN:
                $controller = new AdminController();
                break;
            case AppConfig::USER_WAITER:
                $controller = new WaiterController();
                break;
            default:
                $controller = new ClientController();
                break;
        }
        return $controller;
    }

    private function storeRecord($postObj, $userid)
    {
        $record = new RecordModel();
        $data = array(
            'userid' => $userid,
            'msgtype' => trim($postObj->MsgType),
            'create_time' => intval($postObj->CreateTime),
            'content' => json_encode($postObj, JSON_UNESCAPED_UNICODE)
        );
        return $record->set($data);
    }
}