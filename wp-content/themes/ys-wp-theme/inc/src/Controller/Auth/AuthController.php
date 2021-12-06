<?php

namespace YS\Site\Controller\Auth;

use WP_REST_Request;
use YS\Core\Controller\AbstractController;
use YS\Site\Repository\Auth\AuthRepository;

class AuthController extends AbstractController
{
    public function __construct()
    {
        $this->endpoint = 'users';
        $this->repo     = new AuthRepository();
    }

    public function registerRoutes()
    {
        $this
            ->addRoute('/auth',
                [
                    'methods'  => 'POST',
                    'callback' => [$this, 'auth']
                ]
            )
            ->addRoute('/reset_password',
                [
                    'methods'  => 'PATCH',
                    'callback' => [$this, 'resetPassword']
                ]
            )
            ->addRoute('/registration',
                [
                    'methods'  => 'PUT',
                    'callback' => [$this, 'registration']
                ]
            )
            ->addRoute('/logout',
                [
                    'methods'  => 'DELETE',
                    'callback' => [$this, 'logout']
                ]
            );
    }

    public function auth(WP_REST_Request $request): array
    {
        return $this->repo->auth($request->get_params());
    }

    public function resetPassword(WP_REST_Request $request): array
    {
        return $this->repo->resetPassword($request->get_params());
    }

    public function registration(WP_REST_Request $request): array
    {
        return $this->repo->registration($request->get_params());
    }

    public function logout(WP_REST_Request $request)
    {
        return $this->repo->logout();
    }

    protected function initCustomPages(){}
}