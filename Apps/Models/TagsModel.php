<?php

namespace Apps\Models;

class TagsModel extends CommonModel
{
    public function getTagNames($tagid, $controller = null)
    {
        if (is_int($tagid)) {
            $res = $this->db->where(['id' => $tagid])->select($this->table_name);
            $tagname = $res[0]['tag_name'];
        } else {
            $tagid_list = explode(',', $tagid);
            $tagname = [];
            foreach ($tagid_list as $single_tag) {
                $res = $this->db->where(['id' => intval($single_tag)])->select($this->table_name);
                $tagname[] = array(
                    'name' => $res[0]['tag_name'],
                    'url' => $_SERVER['PHP_SELF'] . '?c=' . $controller . '&tag=' . $single_tag
                );
            }
        }
        return $tagname;
    }

    public function getTagId($tagname)
    {
        $result = $this->db->where(['tag_name' => $tagname])->select($this->table_name);
        return empty($result) ? null : $result[0]['tag_name'];
    }
}