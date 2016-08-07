<?php

namespace Apps\Models;

use Common\Drivers\RedisBase;
use Config\DbConfig;

class PromptModel
{
    protected $redis;
    protected $key;

    public function __construct()
    {
        $this->redis = RedisBase::getSingleton(DbConfig::get('redis'));
        $this->key = 'keyword';
    }

    public function set($keyword)
    {
        return $this->redis->zIncrBy($this->key, 1,$keyword);
    }

    public function get($num)
    {
        $keywords = $this->redis->zRangeByScore($this->key, 0, $num);
        return $keywords;
    }
}