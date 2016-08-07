<?php

namespace Common\Drivers;

class RedisBase
{
    private static $_instance = null;
    private static $_redis = null;
    private $_server;
    private $_port;
    private $_pass;
    private $_database;
    private $_prefix;

    private function __construct()
    {
        if (null === self::$_redis) {
            try {
                $redis = new \Redis();
                $redis->connect($this->_server,$this->_port);
                $redis->auth($this->_pass);
                $redis->select($this->_database);
                $redis->setOption($redis::OPT_PREFIX, $this->_prefix);
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
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
            self::$_instance->_setServer($dsn['REDIS_SERVER']);
            self::$_instance->_setPort($dsn['REDIS_PORT']);
            self::$_instance->_setPassword($dsn['REDIS_PASSWORD']);
            self::$_instance->_setDatabase($dsn['REDIS_DATABASE']);
            self::$_instance->_setPrefix($dsn['REDIS_PREFIX']);
        }
        return self::$_instance;
    }

    private function __clone()
    {
    }

    private function _setServer($server)
    {
        $this->_server = $server;
    }

    private function _setPort($port)
    {
        $this->_port = $port;
    }

    private function _setPassword($pass)
    {
        $this->_pass = $pass;
    }

    private function _setDatabase($database)
    {
        $this->_database = $database;
    }

    private function _setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }

    //记录错误日志
    protected function logError($content)
    {
        $logfile = LOG_DIR . DIRECTORY_SEPARATOR . 'redis_errors.log';
        if (!file_exists($logfile)) fopen($logfile, "w");
        error_log(date("[Y-m-d H:i:s]") . $content . "\n", 3, $logfile);
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