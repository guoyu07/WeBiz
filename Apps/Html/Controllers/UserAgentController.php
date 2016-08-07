<?php

namespace Apps\Html\Controllers;


class UserAgentController
{
    public function start()
    {
        echo $this->output();
    }

    protected function output()
    {
        $twig = $this->getTwig();
        $template = $twig->loadTemplate($this->getTemplate());
        return $template->render($this->getData());
    }

    protected function getTwig()
    {
        if (empty($this->twig)) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Views');
            $this->twig = new \Twig_Environment($loader, array(
                'cache' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Cache',
            ));
        }
        return $this->twig;
    }

    protected function getTemplate()
    {
        return 'basepage.twig';
    }

    protected function getData()
    {
        return array('title' => '抱歉，出错了', 'prompt' => '请在微信客户端打开链接');
    }
}