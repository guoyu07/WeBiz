<?php

namespace Config;

class WxConfig
{
    //微信开发设置
    const WEIXIN_APPID = '';
    const WEIXIN_APPSECRET = '';
    const WEIXIN_TOKEN = 'weixin';
    const REDIS_KEY_ACCESS_TOKEN = 'access_token';
    const REDIS_EXPIRE_ACCESS_TOKEN = 7200;
    const REDIS_KEY_JSAPI_TICKET = 'jsapi_ticket';
    const REDIS_EXPIRE_JSAPI_TICKET = 7200;

    //用户身份定义
    const USER_ADMIN = 100;
    const USER_WAITER = 101;
    const USER_CLIENT = 0;

    //微信设置
    const COMMAND_TEXT = '+';
    const COMMAND_IMAGE = '#';
    const COMMAND_VOICE = '%';

    //客服设置
    const ALLOW_ONE_ON_ONE = true; //是否启用客服一对一模式
}