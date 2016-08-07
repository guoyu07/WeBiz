<?php

namespace Apps\Weixin\Controllers;

use Apps\Models\UserModel;
use Common\Weixin\WxConstants;

class ActionController
{
    protected $user;

    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function doAction($data)
    {
        $method = strtolower(is_string($data) ? $data : $data['action']);
        if (method_exists($this, $method)) {
            if (empty($data['argument'])) {
                $result = $this->$method();
            } else {
                $result = $this->$method($data['argument']);
            }
        } else {
            $result = ['type' => WxConstants::MSGTYPE_TEXT, 'content' => '对不起，您输入的命令有误或者权限不够'];
        }
        return $result;
    }
}
