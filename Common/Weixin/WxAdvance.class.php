<?php

namespace Common\Weixin;

abstract class WxAdvance
{
    abstract protected function getAccessToken();

    abstract protected function feedback($post);

    //获取access_token
    protected function _getAccessToken($appid, $appsecret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;
        return $url;
    }

    //获取jsapi_ticket
    protected function _getJsApiTicket()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $this->getAccessToken() . "&type=jsapi";
        return $url;
    }

    //获取单个用户基本信息
    protected function _getUserInfo($openid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->getAccessToken() . '&openid=' . $openid;
        return $url;
    }

    //批量获取用户基本信息
    protected function _batchGetUserInfo($data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . $this->getAccessToken();
        return array('url' => $url, 'data' => $data);
    }

    //获取用户openid列表
    protected function _getUserList($next_openid = null)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $this->getAccessToken();
        if (isset($next_openid)) $url .= '&next_openid=' . $next_openid;
        return array('url' => $url, 'data' => null);
    }

    //获得全部用户标签
    protected function _getAllTags()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token=' . $this->getAccessToken();
        return array('url' => $url, 'data' => null);
    }

    //创建用户标签
    protected function _createTag($tag)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token=' . $this->getAccessToken();
        $data = json_encode(array('tag' => array('name' => $tag)), JSON_UNESCAPED_UNICODE);
        return array('url' => $url, 'data' => $data);
    }

    //编辑用户标签
    protected function _updateTag($tag)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/update?access_token=' . $this->getAccessToken();
        $data = json_encode(array('tag' => $tag), JSON_UNESCAPED_UNICODE);
        return array('url' => $url, 'data' => $data);
    }

    //删除用户标签
    protected function _delTag($tagid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=' . $this->getAccessToken();
        $data = json_encode(array('tag' => array('id' => $tagid)));
        return array('url' => $url, 'data' => $data);
    }

    /** 用户分组管理方法 */
    //移动用户分组
    protected function _moveUserToGroup($openid, $to_groupid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $this->getAccessToken();
        $data = '{"openid": "' . $openid . '", "to_groupid": ' . $to_groupid . '}';
        return array('url' => $url, 'data' => $data);
    }

    /** 发送主动信息方法 */
    //发送客服消息
    protected function _sendMessage($touser, $type, $data)
    {
        $msg = array('touser' => $touser);
        $msg['msgtype'] = $type;
        $data = trim($data);
        $msg['msgtype'] == WxConstants::MSGTYPE_TEXT ? $msg[$type] = array('content' => urlencode($data)) : $msg[$type] = array('media_id' => urlencode($data));
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $this->getAccessToken();
        $msg = urldecode(json_encode($msg));
        return array('url' => $url, 'data' => $msg);
    }

    //转发消息
    protected function _transferMessage($data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $this->getAccessToken();
        return array('url' => $url, 'data' => $data);
    }

    //根据groupid群发消息
    protected function _massMessageByGroup($groupid, $type, $data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $this->getAccessToken();
        $msg = array('filter' => array('groupid' => $groupid));
        $msg['msgtype'] = $type;
        $data = trim($data);
        $msg[$type] = $msg['msgtype'] == WxConstants::MSGTYPE_TEXT ? array('content' => urlencode($data)) : array('media_id' => urlencode($data));
        $msg = urldecode(json_encode($msg));
        return array('url' => $url, 'data' => $msg);
    }

    //发送模板消息
    protected function _sendTemplateMessage($data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $this->getAccessToken();
        return array('url' => $url, 'data' => $data);
    }

    /** 自定义菜单管理方法 */
    //创建自定义菜单
    protected function _createMenu($menu, $group = null)
    {
        if (empty($group)) {
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->getAccessToken();
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=' . $this->getAccessToken();
        }
        return array('url' => $url, 'data' => $menu);
    }

    //删除自定义菜单
    protected function _delMenu($menuid = null)
    {
        if ($menuid) {
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=' . $this->getAccessToken();
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $this->getAccessToken();
        }
        return array('url' => $url, 'data' => $menuid);
    }

    /** 素材管理方法 */
    //上传永久多媒体素材
    protected function _uploadMaterial($filename, $type)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $this->getAccessToken() . '&type=' . $type;
        $data = array('media' => new \CURLFile($filename));
        return array('url' => $url, 'data' => $data);
    }

    //删除永久多媒体素材
    protected function _delMaterial($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=' . $this->getAccessToken();
        $data = array('media_id' => trim($media_id));
        return array('url' => $url, 'data' => $data);
    }
}