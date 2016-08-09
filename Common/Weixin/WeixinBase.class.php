<?php

namespace Common\Weixin;

use Common\Libs\Http;
use Common\Log\Log;

class WeixinBase
{
    protected static $weixin = null;
    protected static $instance = null;
    protected $appid;
    protected $appsecret;
    protected $redis;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function _setAppId($appid)
    {
        $this->appid = $appid;
    }

    private function _setAppSecret($appsecret)
    {
        $this->appsecret = $appsecret;
    }

    private function _setRedis($redis)
    {
        $this->redis = $redis;
    }

    public static function getWeixin($appid, $appsecret, $redis = null)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            self::$instance->_setAppId($appid);
            self::$instance->_setAppSecret($appsecret);
            self::$instance->_setRedis($redis);
        }
        return self::$instance;
    }

    protected function getAccessToken($flush = false)
    {
        if (!$flush && isset(self::$instance->redis)) {
            $result = $this->_getFromCache('access_token');
        }
        if (!empty($result)) return $result;
        $result = $this->_getAccessTokenFromMp();
        if (!empty($result['access_token'])) {
            if (isset(self::$instance->redis)) $this->_setToCache('access_token', $result['access_token'], 7200);
            return $result['access_token'];
        }
        return false;
    }

    private function _getAccessTokenFromMp()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
        return $this->feedback($url);
    }

    private function _getFromCache($key)
    {
        return self::$instance->redis->get($key);
    }

    private function _setToCache($key, $value, $expire = null)
    {
        return self::$instance->redis->set($key, $value, $expire);
    }

    public function getJsApiParameters()
    {
        $tmpArr = array(
            'noncestr' => $this->_makeNonceStr(),
            'timestamp' => time(),
            'jsapi_ticket' => $this->getJsApiTicket(),
            'url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        );
        $signature = $this->_makeSignature($tmpArr);
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

    protected function getJsApiTicket($flush = false)
    {
        if (!$flush && isset($redis)) {
            $result = $this->_getFromCache('jsapi_ticket');
        }
        if (!empty($result)) return $result;
        $result = $this->_getJsApiTicketFromMp();
        if (!empty($result['ticket'])) {
            if (isset(self::$instance->redis)) $this->_setToCache('jsapi_ticket', $result['ticket'], 7200);
            return $result['ticket'];
        }
        return false;
    }

    //从MP获取jsapi_ticket
    private function _getJsApiTicketFromMp()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $this->getAccessToken() . "&type=jsapi";
        return $this->feedback($url);
    }

    private function _makeNonceStr()
    {
        $codeSet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codes = [];
        for ($i = 0; $i < 16; $i++) {
            $codes[$i] = $codeSet[mt_rand(0, strlen($codeSet) - 1)];
        }
        $nonceStr = implode($codes);
        return $nonceStr;
    }

    private function _makeSignature(array $tmpArr)
    {
        ksort($tmpArr, SORT_STRING);
        $string1 = http_build_query($tmpArr);
        $string1 = urldecode($string1);
        $signature = sha1($string1);
        return $signature;
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
                $pattern = '/access_token=([\w\-]+)(&|$)/';
                $replacement = '\${1}' . self::$instance->getAccessToken(true);
                $post['url'] = preg_replace($pattern, $replacement, $post['url']);
                $result = $this->feedback($post);
            }
        }
        return $result;
    }

    protected function logError($msg)
    {
        $log = Log::Init(LOG_DIR . DIRECTORY_SEPARATOR . 'wechat_errors.log');
        Log::WARN($msg);
    }

    public function close()
    {
        if (!is_null(self::$weixin)) self::$weixin = null;
    }
}