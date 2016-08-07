<?php

namespace Apps\Html\Controllers;

use Apps\Models\VideoModel;

class VideoController extends CommonController
{
    protected function getCustomData()
    {
        $video = new VideoModel();
        $where = [];
        if (!empty($_GET['tag'])) {
            $where['tags'] = ['%'.$_GET['tag'].'%', 'like'];
        }
        if (!empty($_GET['author'])) {
            $author = urldecode($_GET['author']);
            $where['author'] = [$author, '='];
        }
        if (!empty($_GET['live'])) {
            $where['live'] = [1, '='];
        }
        return $video->getCustomData($where);
    }
}