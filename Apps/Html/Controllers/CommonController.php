<?php

namespace Apps\Html\Controllers;

use Apps\Models\ActionModel;
use Apps\Models\PageModel;
use Apps\Models\PromptModel;
use Apps\Models\UserModel;

abstract class CommonController
{
    protected $user;
    protected $twig;
    protected $page;

    public function __construct()
    {
        $this->user = new UserModel();
        $page = new PageModel();
        $this->page = $page->get(array('page' => $this->getTemplate()));
    }

    public function start()
    {
        echo $this->output();
    }

    protected function getTemplate()
    {
        preg_match('/.*\/?([A-Z].*)Controller/', get_class($this), $name);
        return strtolower($name[1]) . '.twig';
    }

    abstract protected function getCustomData();

    protected function getMenu()
    {
        $menu = array(
            array(
                'name' => '专家',
                'url' => $_SERVER['PHP_SELF'],
                'icon' => '../../../Assets/images/icon_nav_msg.png'
            ),
            array(
                'name' => '公开课',
                'url' => $_SERVER['PHP_SELF'] . '?c=video',
                'icon' => '../../../Assets/images/icon_nav_article.png'
            ),
            array(
                'name' => '知识',
                'url' => $_SERVER['PHP_SELF'] . '?c=answers',
                'icon' => '../../../Assets/images/icon_nav_cell.png'
            )
        );
        return $menu;
    }

    protected function getJsApiParmeters()
    {
        $action = new ActionModel();
        return $action->getJsApiParameters();
    }

    protected function getShareData()
    {
        return json_encode(array(
            'title' => $this->getShareTitle(),
            'desc' => $this->getShareDesc(),
            'link' => $this->getShareLink(),
            'imgUrl' => $this->getImgUrl()
        ), JSON_UNESCAPED_UNICODE);
    }

    protected function getShareLink()
    {
        return empty($this->page['link']) ? $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : $this->page['link'];
    }

    protected function getData()
    {
        return array_merge($this->getCommonData(), $this->getCustomData());
    }

    protected function getShareTitle()
    {
        return $this->page['title'];
    }

    protected function getShareDesc()
    {
        return $this->page['desc'];
    }

    protected function getImgUrl()
    {
        return $this->page['imgUrl'];
    }

    protected function getTitle()
    {
        return $this->page['page_title'];
    }

    protected function getPrompts($num)
    {
        $prompt = new PromptModel();
        return $prompt->get($num);
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
                'cache' => CACHE_DIR,
            ));
        }
        return $this->twig;
    }

    protected function getCommonData()
    {
        $data = array(
            'title' => $this->getTitle(),
            'jsapi' => $this->getJsApiParmeters(),
            'shareData' => $this->getShareData(),
            //'prompts' => $this->getPrompts(7),
            'liveurl' => $_SERVER['PHP_SELF'] . '?c=video&live=1',
            'menus' => $this->getMenu()
        );
        return $data;
    }
}