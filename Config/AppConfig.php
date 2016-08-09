<?php

namespace Config;

class WxConfig
{
    //用户身份定义
    const USER_ADMIN = 100;
    const USER_WAITER = 101;
    const USER_CLIENT = 0;

    //微信客服设置
    const COMMAND_TEXT = '+'; //发送文本消息命令符
    const COMMAND_IMAGE = '#'; //发送图片消息命令符
    const COMMAND_VOICE = '%'; //发送语音消息命令符
    const ALLOW_ONE_ON_ONE = true; //是否启用客服一对一模式
}