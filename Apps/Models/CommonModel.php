<?php

namespace Apps\Models;

use Common\Drivers\Database;
use Common\Drivers\RedisBase;
use Common\Drivers\WeixinBase;
use Common\Libs\Config;
use Config\WxPayConfig;

abstract class CommonModel
{
    public static $db;
    public static $redis;
    public static $weixin;
    public static $db_prefix;
    protected $table_name;

    public function __construct()
    {
        self::$db = $this->getDb();
        self::$redis = $this->getRedis();
        self::$weixin = $this->getWexin();
        $this->table_name = $this->getTableName();
    }

    public function get($where = null)
    {
        return $where ? self::$db->where($where)->select($this->table_name) : self::$db->select($this->table_name);
    }

    public function del($where)
    {
        return self::$db->where($where)->delete($this->table_name);
    }

    public function set(array $data, $where = null)
    {
        return empty($where) ? $this->add($data) : self::$db->where($where)->update($this->table_name, $data);
    }

    protected function add(array $data)
    {
        return self::$db->insert($this->table_name, $data);
    }

    protected function getDb()
    {
        $dsn = Config::getConfig('db');
        self::$db_prefix = $dsn['DB_PREFIX'];
        return Database::getSingleton($dsn);
    }

    protected function getRedis()
    {
        $dsn = Config::getConfig('redis');
        return RedisBase::getSingleton($dsn);
    }

    protected function getWexin()
    {
        return WeixinBase::getWeixin(WxPayConfig::APPID, WxPayConfig::APPSECRET, self::$redis);
    }

    protected function getTableName($item = null)
    {
        if (empty($item)) {
            preg_match('/([A-Z][\w]*)Model/', get_class($this), $name);
            $item = strtolower($name[1]);
        }
        return self::$db_prefix . $item;
    }
}