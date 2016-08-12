<?php

namespace Apps\Models;

use Common\Weixin\WxConstants;

class UserModel extends CommonModel
{
    public function get($where)
    {
        if (!empty($where['userid'])) {
            $result = self::$redis->hGet('user' . $where['userid']);
        } elseif (!empty($where['openid'])) {
            $userid = self::$redis->get($where['openid']);
            $result = $userid ? self::$redis->hGet('user' . $userid) : null;
        }
        if (empty($result)) {
            $result = self::$db->where($where)->select($this->table_name);
            if ($result) {
                $result = $result[0];
                self::$redis->set($result['openid'], $result['userid']);
                self::$redis->hMset('user' . $result['userid'], $result);
                return $result;
            } else {
                return empty($where['openid']) ? false : $this->_addUser($where['openid']);
            }
        } else {
            return $result;
        }
    }

    public function set($data, $where = null)
    {
        $result = empty($where) ? $this->add($data) : self::$db->where($where)->update($this->table_name, $data);
        if (!empty($data['userid'])) {
            return self::$redis->hMset('user' . $data['userid'], $data);
        }
        if (!empty($where['userid'])) {
            return self::$redis->hMset('user' . $where['userid'], $data);
        }
        return $result;
    }

    public function update($where, array $user_info = null)
    {
        if (empty($user_info)) {
            if (empty($where['userid'])) {
                $openid = $where['openid'];
            } else {
                $openid = $this->getOpenidByUserid($where['userid']);
            }
            $user_info = $this->_getFromWeixin($openid);
        }
        return $this->set($user_info, $where);
    }

    public function sendMessage($to_userid, $type, $content)
    {
        $openid = $this->getOpenidByUserid($to_userid);
        return $openid ? self::$weixin->sendMessage($openid, $type, $content) : false;
    }

    public function getOpenidByUserid($userid)
    {
        $openid = self::$redis->hGet('user' . $userid);
        if (empty($openid)) {
            $user_info = $this->get(array('userid' => $userid));
            if ($user_info) {
                $openid = $user_info['openid'];
                self::$redis->hMset('user' . $userid, $user_info);
            } else {
                return false;
            }
        } else {
            $openid = $openid['openid'];
        }
        return $openid;
    }

    protected function getUserIdByOpenid($openid)
    {
        $key = trim($openid);
        $userid = self::$redis->get($key);
        if (empty($userid)) {
            $user_info = $this->get(array('openid' => $openid));
            $userid = $user_info['userid'];
            self::$redis->set($key, $userid);
        }
        return $userid;
    }

    public function bind($client, $expert, $expire = null)
    {
        $result = self::$redis->set('user' . $expert . '_partner', $client, $expire);
        return $result && self::$redis->set('user' . $client . '_partner', $expert, $expire);
    }

    public function getPartner($userid)
    {
        return self::$redis->get('user' . $userid . '_partner');
    }

    public function transfer($postObj, $to_userid)
    {
        //$this->sendMessage($to_userid, WxConstants::MSGTYPE_TEXT, $user_info['nickname'] . '：');
        $result = self::$weixin->transfer($postObj, $this->getOpenidByUserid($to_userid));
        if (!empty($result['errcode'])) {
            return array('type' => WxConstants::MSGTYPE_TEXT, 'content' => '非常抱歉, 按照微信的要求, 只能发送语音、文字和图片, 其他格式的内容我看到不到哦');
        } else {
            return '';
        }
    }

    //新用户信息初始化并放入数据库
    private function _addUser($openid)
    {
        $user_info = $this->_getFromWeixin($openid);
        return $this->set($user_info) ? $this->get(array('openid' => $openid)) : null;
    }

    private function _getFromWeixin($openid)
    {
        return self::$weixin->getUser($openid);
    }
}