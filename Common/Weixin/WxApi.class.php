<?php

namespace Common\Weixin;

abstract class WxApi extends WxAdvance
{
    abstract protected function feedback($post);

    //获取单个用户微信基本信息
    public function getUser($openid)
    {
        return $this->feedback($this->_getUserInfo($openid));
    }

    //获取全部用户基本信息，微信单次拉取最大限制为100
    public function batchGetUser(array $user_openid, $limit = 100)
    {
        $count = count($user_openid);
        $times = floor(($count - 1) / $limit);
        $user_info = [];
        //循环获取用户信息
        for ($x = 0; $x <= $times; $x++) {
            //判断每次的起始位置
            $start = $x * $limit;
            //判断每次用户数组的长度，选择微信限制和剩余数的较小者
            $len = min($limit, $count - $start);
            //截取用户openid数组
            $user_slice = array_slice($user_openid, $start, $len);
            $i = 0;
            $data = array('user_list' => []);
            foreach ($user_slice as $user) {
                $i = $i + 1;
                is_array($user) ? $openid = trim($user['openid']) : $openid = trim($user);
                $data['user_list'][$i] = array('openid' => $openid,);
            }
            $result = $this->feedback($this->_batchGetUserInfo(json_encode($data)));
            if ($x === 0) {
                $user_info = $result['user_info_list'];
            } else {
                $user_info = array_merge($user_info, $result['user_info_list']);//拼接的用户数组并非保持有原有的顺序
            }
        }
        return $user_info;
    }

    //获得全部关注者的openid,正常返回openid数组
    public function getUserList($limit = 10000)
    {
        $res = $this->feedback($this->_getUserList());
        if (array_key_exists('total', $res)) {
            $user_list = $res['data']['openid'];
            if ($res['total'] > $limit) {
                $times = floor($res['total'] / $limit) - 1;
                for ($x = 0; $x <= $times; $x++) {
                    $res = $this->_getUserList($res['next_openid']);
                    $next_list = $res['data']['openid'];
                    $user_list = array_merge($user_list, $next_list);
                }
            }
        } else {
            $user_list = $res;
        }
        return $user_list;
    }


    /** 标签管理方法 */
    //获取全部标签
    public function getAllTags()
    {
        return $this->feedback($this->_getAllTags());
    }

    //新增标签
    public function addTag($tag)
    {
        return $this->feedback($this->_createTag($tag));
    }

    //编辑标签
    public function updateTag(array $tag)
    {
        return $this->feedback($this->_updateTag($tag));
    }

    //删除标签
    public function delTag($tagid)
    {
        return $this->feedback($this->_delTag($tagid));
    }

    //移动用户分组
    public function moveUserToGroup($openid, $to_groupid)
    {
        return $this->feedback($this->_moveUserToGroup($openid, $to_groupid));
    }

    /** 发送客服消息方法 */
    //获取转发消息
    protected function getMessage($postObj, $transfer_to_openid)
    {
        $type = trim($postObj->MsgType);
        $post_data = array(
            'touser' => $transfer_to_openid,
            'msgtype' => $type,
            $type => array()
        );
        $origin_data = json_decode(json_encode($postObj, JSON_UNESCAPED_UNICODE), true);
        $keys = array_keys($origin_data);
        $keys_without_value = array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'PicUrl', 'MsgId', 'Format', 'Recognition');
        foreach ($keys as $k) {
            if (!in_array($k, $keys_without_value)) {
                $post_data_key = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $k));
                $post_data[$type][$post_data_key] = urlencode($origin_data[$k]);
            }
        }
        return urldecode(json_encode($post_data));
    }

    //转发用户消息给其他用户,目前仅支持文字、语音和图片
    public function transfer($postObj, $transfer_to_openid)
    {
        return $this->feedback($this->_transferMessage($this->getMessage($postObj, $transfer_to_openid)));
    }

    //发送客服消息
    public function sendMessage($touser, $type, $data)
    {
        return $this->feedback($this->_sendMessage($touser, $type, $data));
    }

    //根据用户分组群发客服消息
    public function massMessageByGroup($groupid, $type, $data)
    {
        return $this->feedback($this->_massMessageByGroup($groupid, $type, $data));
    }

    //发送模板消息
    public function sendTemplateMessage($data)
    {
        return $this->feedback($this->_sendTemplateMessage($data));
    }

    /** 自定义菜单管理方法 */
    //创建自定义菜单
    public function createMenu($menu, $group_id = null)
    {
        return $this->feedback($this->_createMenu($menu, $group_id));
    }

    //删除自定义菜单
    public function delMenu($menuid = null)
    {
        return $this->feedback($this->_delMenu($menuid));
    }

    /** 素材管理方法 */
    //上传永久多媒体素材
    public function uploadMaterial($filename, $type)
    {
        return $this->feedback($this->_uploadMaterial($filename, $type));
    }

    //删除多媒体素材
    public function delMaterial($media_id)
    {
        return $this->feedback($this->_delMaterial($media_id));
    }
}