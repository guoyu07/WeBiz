<?php

namespace Apps\Html\Controllers;


class HomeController extends ExpertController
{
    protected function getTemplate(){
        return 'expert.twig';
    }
}