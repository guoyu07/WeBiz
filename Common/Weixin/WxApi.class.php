<?php

namespace Common\Weixin;

use Common\Libs\Http;

class WxApi extends WxAdvance
{
    protected $appid;
    protected $appsecret;
    protected $redis;
    protected $key;
    protected $expire;
    protected $ticket_key;
    protected $ticket_expire;

    public function __construct($appid, $appsecret, $redis = null, $key = null, $expire = null, $ticket_key = null, $ticket_expire = null)
    {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redis = $redis;
        $this->key = $key;
        $this->expire = $expire;
        $this->ticket_key = $ticket_key;
        $this->ticket_expire = $ticket_expire;
    }

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

    public function getJsApiParameters()
    {
        $tmpArr = array(
            'noncestr' => $this->make_nonceStr(),
            'timestamp' => time(),
            'jsapi_ticket' => $this->getJsApiTicket(),
            'url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        );
        $signature = $this->make_signature($tmpArr);
        $result = array(
            'debug' => false,
            'appId' => $this->appid,
            'timestamp' => $tmpArr['timestamp'],
            'nonceStr' => $tmpArr['noncestr'],
            'signature' => $signature,
            'jsApiList' => array(
                'checkJsApi',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'hideMenuItems',
                'showMenuItems',
                'hideAllNonBaseMenuItem',
                'showAllNonBaseMenuItem',
                'translateVoice',
                'startRecord',
                'stopRecord',
                'onRecordEnd',
                'playVoice',
                'pauseVoice',
                'stopVoice',
                'uploadVoice',
                'downloadVoice',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'downloadImage',
                'getNetworkType',
                'openLocation',
                'getLocation',
                'hideOptionMenu',
                'showOptionMenu',
                'closeWindow',
                'scanQRCode',
                'chooseWXPay',
                'openProductSpecificView',
                'addCard',
                'chooseCard',
                'openCard')
        );
        return json_encode($result);
    }

    protected function make_nonceStr()
    {
        $codeSet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codes = [];
        for ($i = 0; $i < 16; $i++) {
            $codes[$i] = $codeSet[mt_rand(0, strlen($codeSet) - 1)];
        }
        $nonceStr = implode($codes);
        return $nonceStr;
    }

    protected function make_signature(array $tmpArr)
    {
        ksort($tmpArr, SORT_STRING);
        $string1 = http_build_query($tmpArr);
        $string1 = urldecode($string1);
        $signature = sha1($string1);
        return $signature;
    }

    protected function getAccessToken($flush = false)
    {
        if ($this->redis && $this->key && $this->expire) {
            $result = $this->redis->get($this->key);
            if ($flush || empty($result)) {
                $result = $this->feedback($this->_getAccessToken($this->appid, $this->appsecret));
                $result = $result['access_token'];
                $this->redis->set($this->key, $result, $this->expire);
            }
        } else {
            $result = $this->feedback($this->_getAccessToken($this->appid, $this->appsecret));
            $result = $result['access_token'];
        }
        return $result;
    }

    protected function getJsApiTicket($flush = false)
    {
        if ($this->redis && $this->ticket_key && $this->ticket_expire) {
            $result = $this->redis->get($this->ticket_key);
            if ($flush || empty($result)) {
                $result = $this->feedback($this->_getJsApiTicket());
                $result = $result['ticket'];
                $this->redis->set($this->ticket_key, $result, $this->ticket_expire);
            }
        } else {
            $result = $this->feedback($this->_getJsApiTicket());
            $result = $result['ticket'];
        }
        return $result;
    }

    //微信服务器反馈信息处理
    protected function feedback($post)
    {
        $result = Http::request($post);
        $result = json_decode($result, true);
        if (!empty($result['errcode'])) {
            $data = is_string($post) ? $post : json_encode($post, JSON_UNESCAPED_UNICODE);
            $log = "curl_data: $data, " . 'errcode: ' . $result['errcode'] . ', errmsg: ' . $result['errmsg'];
            $this->logError($log);
            $errorno = array(40001, 40014, 42001, 42007);
            if (in_array(intval($result['errcode']), $errorno)) {
                preg_match('/access_token=([\w\-]+)&?/', $post['url'], $old);
                $post['url'] = str_replace($old[1], $this->getAccessToken(true), $post['url']);
                $result = $this->feedback($post);
            }
        }
        return $result;
    }

    //记录错误日志
    protected function logError($content)
    {
        $logfile = LOG_DIR . DIRECTORY_SEPARATOR . 'weixin_errors.log';
        if (!file_exists($logfile)) fopen($logfile, "w");
        error_log(date("[Y-m-d H:i:s]") . $content . "\n", 3, $logfile);
    }
}