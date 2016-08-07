<?php

namespace Apps\Html\Controllers;

use Apps\Models\ArticleModel;
use Apps\Models\ExpertModel;
use Apps\Models\PromptModel;
use Apps\Models\TagsModel;
use Apps\Models\VideoModel;

class QueryController extends CommonController
{
    protected $tags;

    protected function getTitle()
    {
        if (!empty($_GET['keyword'])) {
            $keyword = urldecode($_GET['keyword']);
            $title = '搜索结果　|　' . $keyword;
        } else {
            $title = '未能找到搜索结果';
        }
        return $title;
    }

    protected function getCustomData()
    {
        // TODO: Implement getData() method.
        $keyword = urldecode($_GET['keyword']);
        $prompt = new PromptModel();
        $prompt->set($keyword);
        $data = array_merge($this->getExpert($keyword), $this->getArticle($keyword), $this->getVideo($keyword));
        return $data;
    }

    protected function getVideo($keyword)
    {
        if (empty($this->tags)) $this->tags = new TagsModel();
        $video = new VideoModel();
        $where = array(
            'title' => array("%$keyword%", 'like', 'or'),
            'author' => array("$keyword%", 'like', 'or'),
        );
        $res = $this->tags->getTagId($keyword);
        if ($res) $where['tags'] = ['%' . $res . '%', 'like', 'or'];
        return $video->getCustomData($where, false);
    }

    protected function getArticle($keyword)
    {
        if (empty($this->tags)) $this->tags = new TagsModel();
        $article = new ArticleModel();
        $where = array(
            'title' => array("%$keyword%", 'like', 'or'),
            'author' => array("%$keyword%", 'like', 'or'),
        );
        $res = $this->tags->getTagId($keyword);
        if ($res) $where['tags'] = ['%' . $res . '%', 'like', 'or'];
        return $article->getCustomData($where, false);
    }

    protected function getExpert($keyword)
    {
        $expert = new ExpertModel();
        $where = array(
            'name' => array("%$keyword%", 'like', 'or'),
            'title' => array("%$keyword%", 'like', 'or'),
            'job' => array("%$keyword%", 'like', 'or'),
            'major' => array("%$keyword%", 'like', 'or'),
            'service' => array("%$keyword%", 'like', 'or')
        );
        return $expert->getCustomData($where);
    }
}