<?php

namespace Apps\Models;

class ArticleModel extends CommonModel
{
    public function get($where = null)
    {
        $tags = new TagsModel();
        $result = empty($where) ? self::$db->select($this->table_name) : self::$db->where($where)->select($this->table_name);
        $count = count($result);
        for ($i = 0; $i < $count; $i++) {
            $result[$i]['tags'] = $tags->getTagNames($result[$i]['tags'], 'article');
            $result[$i]['author_url'] = $_SERVER['PHP_SELF'] . '?c=article&author=' . urlencode($result[$i]['author']);
        }
        return $result;
    }

    public function getCustomData($where, $sub = true)
    {
        $subtitle[] = '母婴知识';
        if ($sub) {
            $tags = new TagsModel();
            foreach ($where as $k => $v) {
                switch ($k) {
                    case 'tags':
                        $subtitle[] = is_array($v) ? $tags->getTagNames(intval(str_replace('%', '', $v[0]))) : $tags->getTagNames(intval(str_replace('%', '', $v)));
                        break;
                    case 'author':
                        $subtitle[] = is_array($v) ? $v[0] : $v;
                        break;
                    default:
                        break;
                }
            }
        }
        $data['article_list'] = implode('　|　', $subtitle);
        $data['articles'] = $this->get($where);
        return $data;
    }
}