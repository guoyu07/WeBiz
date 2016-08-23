<?php

namespace Config;

class AppConfig
{
    //验证模式还是生产环境
    const VALID_MODE = false;

    //用户身份定义
    const USER_ADMIN = 100; //管理员组
    const USER_WAITER = 101; //客服组
    const USER_CLIENT = 0; //用户组

    //微信客服设置
    const COMMAND_TEXT = '+'; //发送文本消息命令符
    const COMMAND_IMAGE = '#'; //发送图片消息命令符
    const COMMAND_VOICE = '%'; //发送语音消息命令符
    const ALLOW_ONE_ON_ONE = true; //是否启用客服一对一模式

    //微信Token
    const WEIXIN_TOKEN = 'weixin';

    //百度开发平台access_token
    const BAIDU_AK = '';

    //腾讯开放平台access_token
    const TENCENT_AK = '';
}