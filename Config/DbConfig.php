<?php

namespace Config;

class DbConfig
{
    //Redis连接设置
    const REDIS_SERVER = '';
    const REDIS_PORT = 6379;
    const REDIS_PASSWORD = '';
    const REDIS_DATABASE = 1;
    const REDIS_PREFIX = 'ig_';

    //DB连接设置
    const DB_TYPE = 'mysql';
    const DB_SERVER = 'localhost';
    const DB_PORT = 3306;
    const DB_NAME = '';
    const DB_USER = '';
    const DB_PASSWORD = '';
    const DB_CODE = 'utf8mb4';
    const DB_PCONNECT = false;
    const DB_PREFIX = 'ig_';

    //百度开发设置
    const BAIDU_AK = '';

    //腾讯开发设置
    const TENCENT_AK = '';
}