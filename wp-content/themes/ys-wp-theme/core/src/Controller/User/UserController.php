<?php

namespace YS\Core\Controller\User;

use WP_REST_Request;
use YS\Core\Controller\AbstractController;
use YS\Core\Repository\User\UserRepository;
use YS\Core\Site;

class UserController extends AbstractController
{
    public function __construct()
    {
        $this->endpoint = 'users';
        $this->repo     = new UserRepository();
    }

    protected function initCustomPages() {
        $this->queries[] = 'user_page';

        add_rewrite_endpoint('user_page', EP_ROOT);

        add_rewrite_rule('user/?$', 'index.php?user_page=profile', 'top');

        add_rewrite_rule('login/?$', 'index.php?popup_open=login', 'top');

        add_rewrite_tag('%user_page%', '([^/]+)',  'user_page=');
        add_permastruct('user', 'user/%user_page%', [
            'with_front' => false,
            'paged'      => false,
            'feed'       => false,
            'walk_dirs'  => false,
            'endpoints'  => false,
        ]);

        add_filter('template_include', [$this, 'filterGetUserTemplate'], 99);
        add_action('wp_enqueue_scripts', [$this, 'actionLoadAssets']);
    }

    public function actionLoadAssets()
    {
        $userPage = get_query_var('user_page');
        if (!$userPage) {
            return;
        }

        Site::getInstance()->loadAssets('profile/' . $userPage);
    }

    public function filterGetUserTemplate($template): string
    {
        $userPage = get_query_var('user_page');

        if (!CURRENT_USER_ID && $userPage) {
            wp_safe_redirect(HOME_URL . '/login');
        }

        if ($userPage === 'logout') {
            wp_logout();
            wp_safe_redirect(HOME_URL . '?popup_open=login');
        }

        $newTemplate = locate_template("template-parts/profile/$userPage.php");

        return $newTemplate ?: $template;
    }

    public function registerRoutes(){}
}