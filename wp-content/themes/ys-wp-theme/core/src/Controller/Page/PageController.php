<?php

namespace YS\Core\Controller\page;

use WP_REST_Request;
use YS\Core\Controller\AbstractController;
use YS\Core\Site;

class PageController extends AbstractController
{
    public function __construct()
    {
        add_filter('page_template_hierarchy', [$this, 'filterSetHomeTemplate']);
    }

    protected function initCustomPages() {
        add_action('wp_enqueue_scripts', [$this, 'actionLoadAssets']);
    }

    public function filterSetHomeTemplate($templates)
    {
        array_unshift($templates, 'template-parts/page/' . $templates[0]);
        return $templates;
    }

    public function actionLoadAssets()
    {
        if (is_page()) {
            $page = get_query_var('pagename');
            Site::getInstance()->loadAssets('page/' . $page);
        }
    }

    public function registerRoutes(){}
}