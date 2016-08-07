<?php

namespace Common\Libs;

class Config
{
    public static function getConstants($class, $prefix = null, $namespace = 'Config')
    {
        $class = $namespace . '\\' . $class;
        if (class_exists($class)) {
            $reflect = new \ReflectionClass($class);
            $constants = $reflect->getConstants();
            if (isset($prefix)) {
                $prefix = strtoupper($prefix);
                foreach ($constants as $key => $value) {
                    if (substr($key, 0, strlen($prefix)) == $prefix) $dump[$key] = $value;
                }
                if (!empty($dump)) return $dump;
            } else {
                return $constants;
            }
        }
        return false;
    }
}