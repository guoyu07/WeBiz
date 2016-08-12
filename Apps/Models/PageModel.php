<?php

namespace Apps\Models;

class PageModel extends CommonModel
{
    public function set(array $data, $where = null)
    {
        empty($where) ? $this->add($data) : self::$db->where($where)->update($this->table_name, $data);
        $key = $data['page'];
        return self::$redis->hMset($key, $data);
    }

    public function get($where)
    {
        if (is_array($where) && array_key_exists('page', $where)) {
            $key = $where['page'];
            $result = self::$redis->hGet($key);
        }
        if (empty($result)) {
            $result = self::$db->where($where)->select($this->table_name);
            if (empty($result)) {
                return false;
            } elseif (count($result) == 1) {
                self::$redis->hMset($result[0]['page'], $result[0]);
                return $result[0];
            } else {
                $count = count($result);
                for ($i = 0; $i < $count; $i++) {
                    self::$redis->hMset($result[$i]['page'], $result[$i]);
                }
                return $result;
            }
        } else {
            return $result;
        }
    }
}