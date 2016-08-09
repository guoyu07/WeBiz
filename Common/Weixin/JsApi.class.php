<?php

namespace Common\Weixin;

class JsApi extends WeixinBase
{
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
            'appId' => $this->_appid,
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
            $result = $this->_getJsApiTicketFromCache();
        }
        if (!empty($result)) return $result;
        $result = $this->_getJsApiTicketFromMp();
        if (!empty($result['ticket'])) return $result['ticket'];
        return false;
    }

    private function _getJsApiTicketFromCache(){

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
}