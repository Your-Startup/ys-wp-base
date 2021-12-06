<?php

namespace YS\Site\Repository\Auth;

use YS\Core\Repository\AbstractRepository;

class AuthRepository extends AbstractRepository
{
    public function auth($params): array
    {
        // По логину
        $user = get_user_by('login', $params['name']);
        // По электронной почте
        !$user && $user = get_user_by('email', $params['name']);

        if (!$user || !wp_check_password($params['password'], $user->user_pass, $user->ID)) {
            return [
                'success' => false,
                'message' => 'Неверный логин или пароль'
            ];
        }

        add_filter('auth_cookie_expiration', fn() => YEAR_IN_SECONDS);
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
        remove_filter('auth_cookie_expiration', fn() => YEAR_IN_SECONDS);

        return ['success' => true];
    }

    public function resetPassword($params): array
    {
        // По электронной почте
        $user = get_user_by('email', $params['email']);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email не зарегистрирован'
            ];
        }

        return ['success' => true];
    }

    public function registration($params): array
    {
        // По логину
        if (get_user_by('login', $params['login'])) {
            return [
                'success' => false,
                'message' => 'Логин уже зарегистрирован'
            ];
        }

        // По электронной почте
        if (get_user_by('email', $params['email'])) {
            return [
                'success' => false,
                'message' => 'Email уже зарегистрирован'
            ];
        }

        $password = wp_generate_password(8, false);

        $userId   = wp_insert_user([
            'user_login'   => $params['login'],
            'user_pass'    => $password,
            'user_email'   => $params['email'],
            'display_name' => $params['full_name'],
        ]);

        if (is_wp_error($userId)) {
            return [
                'success' => false,
                'message' => $userId->get_error_message()
            ];
        }

        if (isset($_COOKIE["ys_ref_id"])) {



            update_user_meta($userId, 'user_ref_id', $password);
        }

        update_user_meta($userId, 'ys_save_password', $password);

        update_user_meta($userId, 'user_phone', $params['phone']);
        update_user_meta($userId, 'user_ref_code', $this->createRefCode('u' . $userId));

        return ['success' => true];
    }

    public function logout(): array
    {
        wp_logout();
        return ['success' => true];
    }



    public function find(string $id, ?array $fields = ['all'], string $format = self::ARRAY_FORMAT)
    {
    }

    public function findAll(array $params = [], string $format = self::ARRAY_FORMAT)
    {
    }
}
