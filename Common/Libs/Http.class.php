<?php

namespace Common\Libs;

class Http
{
    /**发起https/http响应,支持get和post两种方法
     * $post = array('url'=> 'url地址', 'data'=> 需要post的数据, 'header'=> html头信息, 'ssl'=> 是否使用ssl);
     */
    public static function request($post)
    {
        $curl = curl_init();
        if (is_array($post)) {
            curl_setopt($curl, CURLOPT_URL, $post['url']);
            $ssl = empty($post['ssl']) ? false : $post['ssl'];
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $ssl);
            if (!empty($post['data'])) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post['data']);
            }
            if (!empty($post['header'])) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $post['header']);
            }
        } else {
            curl_setopt($curl, CURLOPT_URL, $post);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}