<?php

namespace YS\Site\Controller\Referral;

use WP_REST_Request;
use YS\Core\Controller\AbstractController;
use YS\Site\Repository\Auth\AuthRepository;

class ReferralController extends AbstractController
{
    public function __construct()
    {
        $this->repo     = new AuthRepository();
    }

    public function createRefCode(string $string): string
    {
        $b64 = base64_encode($string);

        // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
        $url = strtr($b64, '+/', '-_');
        // Remove padding character from the end of line and return the Base64URL result
        return rtrim($url, '=');
    }

    protected function initCustomPages(){}

    public function registerRoutes(){}
}