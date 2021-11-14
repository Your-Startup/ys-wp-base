<?php
namespace RB\Site\Service;

use RB\Site\Exception\BanException;
use RB\Site\Exception\ForbiddenException;
use RB\Site\Exception\UnauthorizedException;
use RB\Site\Repository\Users\UsersRepository;

class UsersService
{
    /**
     * Создаёт инициалы для пользователя
     *
     * @param $user
     *
     * @return string
     */
    public function getInitials($user): string
    {
        $firstName = $user['firstName'] ?? '';
        $lastName  = $user['lastName'] ?? '';

        if ($firstName && $lastName) {
            $initials = mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1);
        } elseif ($firstName || $lastName) {
            $initials = mb_substr($firstName ?: $lastName, 0, 2);
        } else {
            $initials = mb_substr($user['email'], 0, 2);
        }

        return mb_strtoupper($initials);
    }

    /**
     * Получает статус пользователя
     *
     * @param $user
     *
     * @return string
     */
    public function getLabel($user): string
    {
        $getLabelInfo = \AchHelper::getLabelInfo($user['id']);
        return $getLabelInfo['label'] ?? '';
    }

    /**
     * Получает виды спорта, которыми интересуется игрок
     *
     * @param $user
     *
     * @return array
     */
    public function getSportTypesInterestedIn($user): array
    {
        return \RB\ServiceApi\Auth\Helper::getSelectedSports($user['id']);
    }

    /**
     * Получает имя пользователя в винительном падеже
     *
     * @param $user
     *
     * @return string
     */
    public function getNameInAccusativeCase($user) : string
    {
        return \Base\Helpers\User::getNameCase($user['id'], 3);
    }

    /**
     * Получает имя пользователя в родительном падеже
     *
     * @param $user
     *
     * @return string
     */
    public function getNameInGenitiveCase($user) : string
    {
        return \Base\Helpers\User::getNameCase($user['id'], 1);
    }

    /**
     * Получает данные youtube блока из профиля
     *
     * @param $user
     *
     * @return array
     */
    public function getYoutubeBlock($user)
    {
        if (!isset($user['youtube']) || !$user['youtube']) {
            return null;
        }

        $data = [
            'uri'        => $user['youtube'],
            'banner_img' => rb_get_user_meta('rb_youtube_image',   $user['id']) ?: '',
            'sub_title'  => get_user_option('rb_youtube_subtitle', $user['id']) ?: '',
            'title'      => get_user_option('rb_youtube_title',    $user['id']) ?: ''
        ];

        if (!$data['banner_img'] || !$data['title']) {
            return null;
        }

        return $data;
    }

    /**
     * Получает букмекеров в которых играет пользователь
     *
     * @param $user
     *
     * @return array
     */
    public function getBookmakersIds($user): array
    {
        return \AuthorHelper::selectedBookmakers($user['id']);
    }

    /**
     * Вывод id текущего юзера
     *
     * @return int $userId id текущего юзера
     */
    public static function getCurrentUserId()
    {
        $userId = get_current_user_id();
        return $userId;
    }

    /**
     * Проверяет, совпадает ли пользователь с текущим и выбрасывает исключение, если нет
     *
     * @param int $userId
     *
     * @return bool
     */
    public static function assertCurrentUserId(int $userId)
    {
        if ($userId != self::getCurrentUserId()) {
            throw new ForbiddenException();
        }

        return true;
    }

    /**
     * Проверяет, авторизован ли пользователь и выбрасывает исключение, если нет
     *
     * @return bool
     */
    public static function assertUserLoggedIn()
    {
        if (!self::getCurrentUserId()) {
            throw new UnauthorizedException();
        }

        return true;
    }

    /**
     * Проверяет, забанен ли пользователь и выбрасывает исключение, если да
     *
     * @param int|null $userId
     * @param string $customMessage Сообщение об ошибке
     *
     * @return bool
     */
    public static function assertUserIsBanned(?int $userId = null, string $customMessage = '')
    {
        if (!$userId) {
            $userId = self::getCurrentUserId();
        }

        if (!$userId) {
            return false;
        }

        $userRepo = new UsersRepository();
        $user = $userRepo->find($userId, ['roles']);

        if (in_array('banned', $user['roles'], true)) {
            throw new BanException($customMessage);
        }

        return false;
    }

    /**
     * @param null|string $role
     *
     * @return string
     */
    public static function getUserRole(?string $role): string
    {
        $allRoles = [
            'administrator'           => __('Администратор', 'bmr'),
            'editor'                  => __('Редактор', 'bmr'),
            'author'                  => __('Автор', 'bmr'),
            'contributor'             => __('Участник', 'bmr'),
            'subscriber'              => __('Подписчик', 'bmr'),
            'mod_complaints'          => __('Модератор жалоб', 'bmr'),
            'mod_custom'              => __('Подкомитет', 'bmr'),
            'bookmaker'               => __('Представитель БК', 'bmr'),
            'bookmaker_assistant'     => __('Помощник представителя БК', 'bmr'),
            'rb_member'               => __('Член команды «РБ»', 'bmr'),
            'author_plus'             => __('Автор+', 'bmr'),
            'banned'                  => __('Заблокированный', 'bmr'),
            'kapper'                  => __('Каппер', 'bmr'),
            'kapper_agent'            => __('Представитель каппера', 'bmr'),
            'forecaster'              => __('Прогнозист', 'bmr'),
            'journalist'              => __('Журналист', 'bmr'),
            'technical_director'      => __('Технический директор', 'bmr'),
            'developer'               => __('Разработчик', 'bmr'),
            'founder'                 => __('Основатель', 'bmr'),
            'chief_editor'            => __('Главный редактор', 'bmr'),
            'designer'                => __('Дизайнер', 'bmr'),
            'art_director'            => __('Арт-директор', 'bmr')
        ];

        return $allRoles[$role];
    }

    /**
     * Проверяет текущего пользователя на анонима
     *
     * @param int $userId
     *
     * @return bool
     */
    public function isAnonymous(int $userId): bool
    {
        $isAnonymous = false;

        $user = get_user_by('id', $userId);

        if (!$user) {
            return $isAnonymous;
        }

        $userEeId         = get_user_meta($user->ID, 'user_eeid', true);
        $emptyEmail       = (bool)get_user_meta($user->ID, '_empty_email', true);
        $isAnonymousEmail = ($emptyEmail || $user->user_email === 'rb_user_' . $userEeId . '@brl.ru');

        $anonymousName   = sprintf(__('Аноним %d'), $user->ID);
        $emptyName       = (bool)get_user_meta($user->ID, '_empty_name', true);
        $isAnonymousName = ($emptyName || $user->first_name === $anonymousName);

        return $isAnonymousEmail || $isAnonymousName;
    }

    public function updateMetaData($userId, $data)
    {
        if (isset($data['sport_types_interested_in'])) {
            $selectedSports = array_unique($data['sport_types_interested_in']);
            $selectedSports && update_user_option($userId, 'sport_type_interests', $selectedSports);
        }

        if (isset($data['onesignal_user_ids'])) {
            update_user_meta($userId, '_onesignal_user_ids', $data['onesignal_user_ids']);
            setcookie("onesignal-user-id", 1);
        }

        if (isset($data['bookmakers'])) {
            $selectedBookmakers = array_unique($data['bookmakers']);
            $selectedBookmakers && update_user_meta($userId, 'selected_bookmakers_1', $data['bookmakers']);
        }

        if (isset($data['gender'])) {
            update_user_meta($userId, 'gender', $data['gender']);
        }

        if (isset($data['birthday'])) {
            update_user_meta($userId, 'birdthday', $data['birthday']);
        }

        if (isset($data['biography'])) {
            update_user_meta($userId, 'description', $data['biography']);
        }

        if (isset($data['socials'])) {
            foreach ($data['socials'] as $network => $value) {
                $value && update_user_meta($userId, $network, $value);
            }
        }

        if (isset($data['notifications_settings'])) {
            update_user_option($userId, \ProfileHelper::SITE_OPTION, $data['notifications_settings']);
        }
    }
}
