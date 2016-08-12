<?php

namespace Config;

class DbConfig
{
    //Redis连接设置
    const REDIS_SERVER = 'localhost';  //Redis服务器url
    const REDIS_PORT = 6379; //Redis端口
    const REDIS_PASSWORD = ''; //Redis连接密码
    const REDIS_DATABASE = 0; //Redis数据库
    const REDIS_PREFIX = 'wb_'; //Redis键名前缀

    //DB连接设置
    const DB_TYPE = 'mysql'; //数据库类型
    const DB_SERVER = 'localhost'; //数据库地址
    const DB_PORT = 3306; //数据库端口
    const DB_NAME = ''; //数据库名字
    const DB_USER = ''; //数据库用户名
    const DB_PASSWORD = ''; //数据库密码
    const DB_CODE = 'utf8mb4'; //数据库编码
    const DB_PCONNECT = false; //是否长连接
    const DB_PREFIX = 'wb_'; //数据库表前缀
}