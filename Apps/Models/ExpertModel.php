<?php

namespace Apps\Models;

class ExpertModel extends CommonModel
{
    public function get($where = false)
    {
        return $where ? self::$db->where($where)->order('status DESC')->select($this->table_name) : self::$db->order('status DESC')->select($this->table_name);
    }

    public function getCustomData($where = null)
    {
        $user = new UserModel();
        $expert_list = $this->get($where);
        foreach ($expert_list as $single_expert) {
            $user_info = $user->get(array('userid' => $single_expert['userid']));
            $expert_id = $single_expert['id'];
            $linkurl = $_SERVER['PHP_SELF'] . '?c=order&expert=' . $expert_id;
            $expert[] = array(
                'linkurl' => $linkurl,
                'headimgurl' => $user_info['headimgurl'],
                'title' => $single_expert['name'],
                'desc' => $single_expert['title'] . '，' . $single_expert['major']
            );
        }
        $data = array(
            'expert_list' => '专家列表',
            'experts' =>  isset($expert) ? $expert : null
        );
        return $data;
    }
}