<?php

namespace Apps\Models;


class PromptModel extends CommonModel
{
    protected $key = 'keyword';

    public function set($keyword)
    {
        return self::$redis->zIncrBy($this->key, 1, $keyword);
    }

    public function get($num)
    {
        return self::$redis->zRangeByScore($this->key, 0, $num);
    }
}