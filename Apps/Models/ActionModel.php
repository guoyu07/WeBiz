<?php

namespace Apps\Models;

use Common\Libs\File;

class ActionModel extends CommonModel
{
    public function flushredis()
    {
        return self::$redis->flushdb();
    }

    public function refresh($dir = null)
    {
        if (empty($dir)) $dir = ROOT . 'Apps' . DIRECTORY_SEPARATOR . 'Html' . DIRECTORY_SEPARATOR . 'Cache';
        return File::delAllFiles($dir);
    }

    public function getJsApiParameters(){
        return self::$weixin->getJsApiParameters();
    }
}