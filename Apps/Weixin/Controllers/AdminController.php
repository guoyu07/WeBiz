<?php

namespace Apps\Weixin\Controllers;


class AdminController extends WaiterController
{
    protected function getActionController(){
        return new AdminActionController();
    }
}