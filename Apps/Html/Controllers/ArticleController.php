<?php

namespace Apps\Html\Controllers;

use Apps\Models\ArticleModel;

class ArticleController extends CommonController
{
    protected function getCustomData()
    {
        $article = new ArticleModel();
        $where = [];
        if (!empty($_GET['tag'])) {
            $where['tags'] = ['%'.$_GET['tag'].'%', 'like'];
        }
        if (!empty($_GET['author'])) {
            $where['author'] = [urldecode($_GET['author']), '='];
        }
        return $article->getCustomData($where);
    }
}