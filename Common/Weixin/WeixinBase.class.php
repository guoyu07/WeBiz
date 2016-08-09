<?php

namespace Common\Weixin;

use Common\Libs\Http;
use Common\Log\Log;

class WeixinBase
{
    protected static $weixin = null;
    protected static $instance = null;
    protected $_appid;
    protected $_appsecret;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function _setAppId($appid)
    {
        $this->_appid = $appid;
    }

    private function _setAppSecret($appsecret)
    {
        $this->_appsecret = $appsecret;
    }

    public static function getWeixin($appid, $appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            self::$instance->_setAppId($appid);
            self::$instance->_setAppSecret($appsecret);
        }
        return self::$instance;
    }

    protected function getAccessToken($flush = false, $redis = null)
    {
        if (!$flush && isset($redis)) {
            $result = $this->_getAccessTokenFromCache();
        }
        if (!empty($result)) return $result;
        $result = $this->_getAccessTokenFromMp();
        if (!empty($result['access_token'])) return $result['access_token'];
        return false;
    }

    private function _getAccessTokenFromMp()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->_appid . '&secret=' . $this->_appsecret;
        return $this->feedback($url);
    }

    private function _getAccessTokenFromCache()
    {

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