<?php

namespace Apps\Html\Controllers;

use Apps\Models\ArticleModel;

class AnswersController extends CommonController
{
    protected function getCustomData()
    {
        // TODO: Implement getCustomData() method.
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