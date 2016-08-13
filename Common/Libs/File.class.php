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
}