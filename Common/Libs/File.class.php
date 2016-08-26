<?php

namespace Common\Libs;

class File
{
    //删除目录
    public static function delAllFiles($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . DIRECTORY_SEPARATOR . $file;
                if (!is_dir($fullpath)) {
                    if (unlink($fullpath) == false) return false;
                } else {
                    self::delAllFiles($fullpath);
                    if (rmdir($fullpath) == false) return false;
                }
            }
        }
        closedir($dh);
        return true;
    }

    //检查目录是否存在，若不存在则创建
    public static function checkDir($dir){
        if (is_string($dir)){
            if (!is_dir($dir)) mkdir($dir);
        }else{
            foreach ($dir as $d) {
                if (!is_dir($d)) mkdir($d);
            }
        }
    }
}