<?php

namespace YS\Core\Controller\Home;

use WP_REST_Request;
use YS\Core\Controller\AbstractController;
use YS\Core\Site;

class HomeController extends AbstractController
{
    public function __construct()
    {
        $this->endpoint = 'users';
    }

    protected function initCustomPages() {
        add_filter('frontpage_template_hierarchy', [$this, 'filterSetHomeTemplate']);
        add_filter('home_template_hierarchy', [$this, 'filterSetHomeTemplate']);
        add_action('wp_enqueue_scripts', [$this, 'actionLoadAssets']);
    }

    public function filterSetHomeTemplate($templates)
    {
        array_unshift($templates, 'template-parts/home.php');
        return $templates;
    }

    public function actionLoadAssets()
    {
        if (is_home() || is_front_page()) {
            Site::getInstance()->loadAssets('home/home');
        }
    }

    public function registerRoutes(){}
}