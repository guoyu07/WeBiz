<?php

namespace Apps\Models;

use Common\Weixin\WxApi;
use Common\Drivers\Database;
use Common\Drivers\RedisBase;
use Config\DbConfig;
use Config\WxConfig;

abstract class CommonModel
{
    public $db;
    public $redis;
    public $weixin;
    public $db_prefix;
    protected $table_name;

    public function __construct()
    {
        $this->db = $this->getDb();
        $this->redis = $this->getRedis();
        $this->weixin = $this->getWexin();
        $this->table_name = $this->getTableName();
    }

    public function get($where = null)
    {
        return $where ? $this->db->where($where)->select($this->table_name) : $this->db->select($this->table_name);
    }

    public function del($where)
    {
        return $this->db->where($where)->delete($this->table_name);
    }

    public function set(array $data, $where = null)
    {
        return empty($where) ? $this->add($data) : $this->db->where($where)->update($this->table_name, $data);
    }

    protected function add(array $data)
    {
        return $this->db->insert($this->table_name, $data);
    }

    protected function getDb()
    {
        $dsn = DbConfig::get('db');
        $this->db_prefix = $dsn['DB_PREFIX'];
        return empty($this->db) ? $this->db = Database::getSingleton($dsn) : $this->db;
    }

    protected function getRedis()
    {
        $dsn = DbConfig::get('redis');
        return empty($this->redis) ? $this->redis = RedisBase::getSingleton($dsn) : $this->redis;
    }

    protected function getWexin()
    {
        if (empty($this->weixin)) {
            $appid = WxConfig::WEIXIN_APPID;
            $appsecret = WxConfig::WEIXIN_APPSECRET;
            $key = WxConfig::REDIS_KEY_ACCESS_TOKEN;
            $expire = WxConfig::REDIS_EXPIRE_ACCESS_TOKEN;
            $ticket_key = WxConfig::REDIS_KEY_JSAPI_TICKET;
            $ticket_expire = WxConfig::REDIS_EXPIRE_JSAPI_TICKET;
            $this->weixin = new WxApi($appid, $appsecret, $this->redis, $key, $expire, $ticket_key, $ticket_expire);
        }
        return $this->weixin;
    }

    protected function getItem()
    {
        preg_match('/([A-Z][\w]*)Model/', get_class($this), $name);
        return strtolower($name[1]);
    }

    protected function getTableName($item = null)
    {
        $table = array(
            'admin' => 'admin',
            'record' => 'record',
            'order' => 'order',
            'user' => 'user',
            'waiter' => 'waiter',
            'menu' => 'menu',
            'autoreply' => 'autoreply',
            'expert' => 'expert',
            'article' => 'article',
            'tags' => 'tags',
            'page' => 'page',
            'video' => 'video'
        );
        return $this->db_prefix . ($item ? $table[$item] : $table[$this->getItem()]);
    }
}