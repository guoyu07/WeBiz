<?php

namespace Common\Libs;

class File
{
    public static function delAllFiles($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . DIRECTORY_SEPARATOR . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::delAllFiles($fullpath);
                    rmdir($fullpath);
                }
            }
        }
        closedir($dh);
    }
}