<?php

namespace RB\Site\Service;

class CommentsService
{
    /**
     * Получает массив с лайками
     *
     * @param string $value Массив со свойствами комментария
     *
     * @return array Массив с лайками
     */
    public function getLikes($value)
    {
        $likes = unserialize($value);
        if (!is_array($likes)) {
            $likes = [];
        }

        if (isset($likes[0])) {
            unset($likes[0]);
        }

        $userId     = UsersService::getCurrentUserId();
        $likesCount = array_count_values($likes);

        $userLike      = $likes[$userId] ?? 0;
        $likesTotal    = $likesCount[1]  ?? 0;
        $dislikesTotal = $likesCount[-1] ?? 0;

        return [
            'user_like' => $userLike,
            'likes'     => $likesTotal,
            'dislikes'  => $dislikesTotal
        ];
    }

    /**
     * Добавляет/удаляет лайк/дизлайк
     *
     * @param string $likesData
     * @param string $type
     *
     * @return array $likesData Массив с лайками комментария
     */
    public function mapLikesData(string $likesData, string $type): array
    {
        $userId = UsersService::getCurrentUserId();

        // Проверка типа (лайк или дизлайк)
        $likeValue = ($type === 'like') ? 1 : -1;

        // Получает текущий статус у комментария
        try {
            $likesData = unserialize($likesData);
            if (!is_array($likesData)) {
                $likesData = [];
            }
        } catch (\Exception $e) {
            $likesData = [];
        }

        // Формирует новый массив с лайками/дизлайками для комментария
        if (array_key_exists($userId, $likesData)) {
            if ($likesData[$userId] == $likeValue) {
                unset($likesData[$userId]);
            } else {
                $likesData[$userId] = $likeValue;
            }
        } else {
            $likesData[$userId] = $likeValue;
        }

        return $likesData;
    }

    /**
     * Подготавливает данные для вставки через wp_insert_comment/wp_update_comment
     *
     * @param array $data Параметры запроса или данные из сущности
     *
     * @return array Подготовленные даныне
     */
    public function mapDataToCommentColumns(array $data): array
    {
        $commentMap = [
            'id'        => 'comment_ID',
            'object_id' => 'comment_post_ID',
            'content'   => 'comment_content',
            'parent_id' => 'comment_parent',
            'date'      => 'comment_date_gmt',
            'status'    => 'comment_approved',
            'author_id' => 'user_id'
        ];
        $metaMap = [
            'is_pinned' => 'pinned',
            'attachments' => 'attachments'
        ];

        // Дополнительная фильтрация
        if (isset($data['status'])) {
            $data['status'] = $this->mapStatusFieldValue($data['status']);
        }
        if (isset($data['is_pinned'])) {
            $data['is_pinned'] = ($data['is_pinned']) ? '1' : '0';
        }
        if (isset($data['attachments'])) {
            $data['attachments'] = array_filter(array_map('intval', $data['attachments']));
        } else {
            $data['attachments'] = [];
        }

        // Преобразовываем данные в новый массив
        $result = [];
        foreach ($commentMap as $key => $field) {
            if (isset($data[$key])) {
                $result[$field] = $data[$key];
            }
            if ($key === 'date') {
                $result[$field] = date('Y-m-d H:i:s', $data[$key]);
            }
        }

        $result['comment_meta'] = [];
        foreach ($metaMap as $key => $field) {
            if (isset($data[$key])) {
                $result['comment_meta'][$field] = $data[$key];
            }
        }

        return $result;
    }

    /**
     * Проверяет, что данные содержат только обязательные и мета поля
     *
     * @param $data array Данные, результат mapDataToCommentInsert()
     * @return bool
     */
    public function dataContainsOnlyMeta(array $data): bool
    {
        $allowedKeys = ['comment_ID', 'comment_post_ID', 'comment_meta'];

        foreach ($data as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Заменяет некоторые статусы комментариев на совместимые с wp_set_comment_status
     *
     * @param string $status
     * @see https://github.com/WordPress/WordPress/blob/5.3-branch/wp-includes/comment.php#L2246
     *
     * @return string
     */
    public function mapStatusFieldValue(string $status)
    {
        if (in_array($status, ['approved', 'approve', '1'], true)) {
            return '1';
        }
        if (in_array($status, ['unapproved', 'hold', '0'], true)) {
            return '0';
        }
        if ($status == 'spam') {
            return 'spam';
        }
        if ($status == 'trash') {
            return 'trash';
        }

        return false;
    }

    /**
     * Конвертируем статус комментария в читаемый формат
     *
     * @param string $status Статус комментария
     * @see https://github.com/WordPress/WordPress/blob/5.3-branch/wp-includes/comment.php#L1650
     *
     * @return string|bool
     */
    public function mapStatusToReadableValue(string $status)
    {
        if (in_array($status, ['0', 'hold', 'unapproved'])) {
            return 'unapproved';
        }
        elseif (in_array($status, ['1', 'approve', 'approved'])) {
            return 'approved';
        }
        elseif ($status == 'spam') {
            return 'spam';
        }
        elseif ($status == 'trash') {
            return 'trash';
        }
        else {
            return '';
        }
    }
}