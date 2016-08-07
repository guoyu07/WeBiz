<?php

namespace Common\Drivers;

interface DataInCache
{
    public function getFromCache($key, $expire);

    public function setIntoCache($key, $value, $expire);
}