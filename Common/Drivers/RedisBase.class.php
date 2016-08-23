<?php

namespace Common\Drivers;

use Common\Log\Log;

class RedisBase
{
    private static $_instance = null;
    private static $_redis = null;

    private function __construct(array $dsn)
    {
        if (null === self::$_redis) {
            try {
                $redis = new \Redis();
                $redis->connect($dsn['REDIS_SERVER'], $dsn['REDIS_PORT']);
                $redis->auth($dsn['REDIS_PASSWORD']);
                $redis->select($dsn['REDIS_DATABASE']);
                $redis->setOption($redis::OPT_PREFIX, $dsn['REDIS_PREFIX']);
                self::$_redis = $redis;
            } catch (\RedisException $e) {
                $errMsg = $e->getMessage();
                $this->logError($errMsg);
                die('Redis connection failed: ' . $errMsg);
            }
        }
    }

    public static function getSingleton(array $dsn)
    {
        if (null === self::$_instance) {
            self::$_instance = new self($dsn);
        }
        return self::$_instance;
    }

    private function __clone()
    {
    }

    //记录错误日志
    protected function logError($msg)
    {
        $log = Log::Init(LOG_DIR . DIRECTORY_SEPARATOR . 'redis_errors.log');
        Log::WARN($msg);
    }

    public function get($key)
    {
        return self::$_redis->get($key);
    }

    public function set($key, $value, $expire = null)
    {
        $result = self::$_redis->set($key, $value);
        if ($expire) {
            $result = self::$_redis->expire($key, $expire);
        }
        return $result;
    }

    public function hSet($key, $field, $value)
    {
        return self::$_redis->hSet($key, $field, $value);
    }

    public function hMset($key, array $data)
    {
        return self::$_redis->hMset($key, $data);
    }

    public function hGet($key, $data = null)
    {
        if (empty($data)) {
            return self::$_redis->hGetAll($key);
        }
        if (is_string($data)) {
            return self::$_redis->hGet($key, $data);
        } else {
            return self::$_redis->hMGet($key, $data);
        }
    }

    public function hIncrBy($key, $field, $value)
    {
        return self::$_redis->hIncrBy($key, $field, $value);
    }

    public function zIncrBy($key, $value, $member)
    {
        return self::$_redis->zIncrBy($key, $value, $member);
    }

    public function zRangeByScore($key, $start, $stop)
    {
        return self::$_redis->zRangeByScore($key, $start, $stop);
    }

    public function del($key)
    {
        return self::$_redis->del($key);
    }

    public function flushdb()
    {
        return self::$_redis->flushDB();
    }

    public function close()
    {
        if (!is_null(self::$_redis)) self::$_redis = null;
    }
}