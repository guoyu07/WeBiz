<?php

namespace Apps\Models;

class VideoModel extends CommonModel
{
    public function get($where = null)
    {
        return empty($where) ? $this->db->order('live DESC')->select($this->table_name) : $this->db->where($where)->order('live DESC')->select($this->table_name);
    }

    public function getCustomData($where, $sub = true)
    {
        $tags = new TagsModel();
        $subtitle[] = '专家公开课';
        if ($sub) {
            foreach ($where as $k => $v) {
                switch ($k) {
                    case 'tags':
                        $subtitle[] = is_array($v) ? $tags->getTagNames(intval(str_replace('%', '', $v[0]))) : $tags->getTagNames(intval(str_replace('%', '', $v)));
                        break;
                    case 'author':
                        $subtitle[] = is_array($v) ? $v[0] : $v;
                        break;
                    case 'live':
                        $is_live = is_array($v) ? $v[0] : $v;
                        if ($is_live) $subtitle[] = '正在直播';
                        break;
                    default:
                        break;
                }
            }
        }
        $data['video_list'] = implode('　|　', $subtitle);
        $video_list = $this->get($where);
        $count = count($video_list);
        for ($i = 0; $i < $count; $i++) {
            $video_list[$i]['id'] = 'xvid' . $video_list[$i]['id'];
            $video_list[$i]['tags'] = $tags->getTagNames($video_list[$i]['tags'], 'video');
            $video_list[$i]['author_url'] = $_SERVER['PHP_SELF'] . '?c=video&author=' . urlencode($video_list[$i]['author']);
        }
        $data['videos'] = $video_list;
        return $data;
    }
}