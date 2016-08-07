<?php

namespace Apps\Html\Controllers;

use Apps\Models\ExpertModel;

class ExpertController extends CommonController
{
    protected function getCustomData()
    {
        $expert = new ExpertModel();
        $expert_list = $expert->getCustomData();
        return $expert_list;
    }
}