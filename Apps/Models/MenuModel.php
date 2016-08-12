<?php

namespace Apps\Models;

class MenuModel extends CommonModel
{
    public function get($id = null)
    {
        $table = $this->table_name . $id;
        $type = array('view' => 'url', 'click' => 'key');
        $button = [];
        $data = self::$db->field('type, name, value, group')->select($table);
        foreach ($data as $v) {
            $group = $v['group'];
            if (!array_key_exists($group, $button)) {
                $button[$group] = [];
            }
            if (empty($v['type']) || empty($v['value'])) {
                $button[$group] = ['name' => $v['name'], 'sub_button' => []];
            } else {
                $x = array(
                    'name' => $v['name'],
                    'type' => $v['type'],
                    $type[$v['type']] => $v['value']
                );
                if (array_key_exists('sub_button', $button[$group])) {
                    $button[$group]['sub_button'][] = $x;
                } else {
                    $button[$group] = $x;
                }
            }
        }
        $menu = ['button' => $button];
        if (!empty($id)) {
            $menu['matchrule'] = $id;
        }
        $menu = json_encode($menu, JSON_UNESCAPED_UNICODE);
        return $menu;
    }
}