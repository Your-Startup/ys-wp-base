<?php
namespace YS\Core\Util;

use YS\Core\Entity\AbstractEntity;
use YS\Core\Entity\Forecasts\ForecastEntity;
use YS\Core\Entity\Reviews\BookmakerEntity;
use YS\Core\Entity\Users\UserEntity;

class SeoUtil
{
    /**
     * Получает SEO заголовок для поста
     *
     * @param AbstractEntity $entity
     *
     * @return string
     */
    public static function getPostSeoTitle(AbstractEntity $entity)
    {
        if (!class_exists('WPSEO_Meta')) {
            return '';
        }

        $post = get_post($entity->getId(), ARRAY_A);

        $title = \WPSEO_Meta::get_value('title', $post['ID']);
        if (!$title) {
            $title = \WPSEO_Options::get('title-' . $post['post_type']);
        }

        $title = self::correctingSeoTitle($title);
        $title = self::replaceSnippets($title, 'description', $entity, $post);

        return htmlspecialchars_decode(wp_strip_all_tags(stripslashes($title), true), ENT_QUOTES);
    }

    /**
     * Получает SEO описание для поста
     *
     * @param AbstractEntity $entity
     *
     * @return string
     */
    public static function getPostSeoDescription(AbstractEntity $entity)
    {
        if (!class_exists('WPSEO_Meta')) {
            return '';
        }

        $post = get_post($entity->getId(), ARRAY_A);

        $description = \WPSEO_Meta::get_value('metadesc', $post['ID']);
        if (!$description) {
            $description = \WPSEO_Options::get('metadesc-' . $post['post_type']);
        }

        $description = self::replaceSnippets($description, 'description', $entity, $post);

        // Если Yaost не отдал, то берем из $post
        $description = !empty($description) ? $description : $post['post_excerpt'];

        return htmlspecialchars_decode(wp_strip_all_tags(stripslashes($description), true), ENT_QUOTES);
    }

    /**
     * Получает SEO заголовок для таксономии
     *
     * @param int $id
     * @param string $taxonomy
     *
     * @return string
     */
    public static function getTaxonomySeoTitle(int $id, string $taxonomy)
    {
        if (!$taxonomy) {
            return '';
        }

        if (!class_exists('WPSEO_Taxonomy_Meta')) {
            return '';
        }

        $term = get_term($id, $taxonomy, ARRAY_A);

        $title = \WPSEO_Taxonomy_Meta::get_term_meta($term['term_id'], $taxonomy, 'title');
        if (!$title) {
            $title = \WPSEO_Options::get('title-tax-' . $taxonomy);
        }

        $title = self::correctingSeoTitle($title);

        $replacer = new \WPSEO_Replace_Vars();
        $title = $replacer->replace($title, $term);
        $title = apply_filters('wpseo_title', $title);

        return htmlspecialchars_decode(wp_strip_all_tags(stripslashes($title), true), ENT_QUOTES);
    }

    /**
     * Получает SEO описание для таксономии
     *
     * @param int $id
     * @param string $taxonomy
     *
     * @return string
     */
    public static function getTaxonomySeoDescription(int $id, string $taxonomy)
    {
        if (!$taxonomy) {
            return '';
        }

        if (!class_exists('WPSEO_Taxonomy_Meta')) {
            return '';
        }

        $term = get_term($id, $taxonomy, ARRAY_A);

        $description = \WPSEO_Taxonomy_Meta::get_term_meta($term['term_id'], $taxonomy, 'title');
        if (!$description) {
            $description = \WPSEO_Options::get('metadesc-tax-' . $taxonomy);
        }

        $replacer = new \WPSEO_Replace_Vars();
        $description = $replacer->replace($description, $term);
        $description = apply_filters('wpseo_metadesc', $description);

        // Если Yaost не отдал, то берем из $term
        $description = !empty($description) ? $description : $term['description'];

        return htmlspecialchars_decode(wp_strip_all_tags(stripslashes($description), true), ENT_QUOTES);
    }

    /**
     * Получает SEO описание для пользователя
     *
     * @param AbstractEntity $entity
     *
     * @return string
     */
    public static function getUserSeoDescription(AbstractEntity $entity)
    {
        if (!class_exists('WPSEO_Meta')) {
            return '';
        }

        $description = get_the_author_meta('wpseo_metadesc', $entity->getId());
        if (!$description) {
            $description = \WPSEO_Options::get('metadesc-author-wpseo');
        }

        $description = self::replaceSnippets($description, 'description', $entity);
        if (!$description) {
            return '';
        }

        return htmlspecialchars_decode(wp_strip_all_tags(stripslashes($description), true), ENT_QUOTES);
    }

    /**
     * Получает SEO адрес картинки
     *
     * @param int $id
     * @param string $imageUri
     *
     * @return string
     */
    public static function getSeoImage($id, $imageUri)
    {
        $defaultImage = site_url() . '/wp-content/themes/bmr/redesign/dist/images/ru/logo/logo-social.png';

        if ($imageFromDb = get_post_meta($id, 'social_img', true)) {
            $post = get_post($id);
            $v = '?v=' . strtotime($post->post_modified);
            $image = $imageFromDb['url'] . $v;
        } else {
            $image = $imageUri;
        }

        return $image ? $image : $defaultImage;
    }

    /**
     * Заменяет пользовательские сниппеты в строке на нужные данные
     *
     * @param AbstractEntity $entity
     * @param string $string
     *
     * @return string
     */
    private static function replaceCustomSnippets(AbstractEntity $entity, string $string)
    {
        switch (get_class($entity)) {
            // Users
            case UserEntity::class :
                $firstName = $entity->getFirstName();
                $lastName  = $entity->getLastName();

                if ($firstName && $lastName) {
                    $name = $firstName . ' ' . $lastName;
                } else {
                    $name = $firstName ?: $entity->getName();
                }

                $string = str_replace('%%name%%', $name, $string);
                break;

            // Forecasts
            case ForecastEntity::class :
                $date = $entity->getEventDate()
                    ? date('d.m.Y', $entity->getEventDate())
                    : get_the_date('d.m.Y', $entity->getId());

                $string = str_replace(':::bmr_forecast_data:::', $date, $string);
                break;

            // Bookreviews
            case BookmakerEntity::class :
                $string = str_replace('%%review_bookmaker_name%%', $entity->getCompanyName(), $string);
                $string = str_replace('%%review_feedbacks_count%%', $entity->getFeedbacksCount(), $string);
                $string = str_replace('%%review_comments_count%%', $entity->getCommentsCount(), $string);
                $string = str_replace('%%review_questions_count%%', $entity->getQuestionsCount(), $string);
                $string = str_replace('%%review_complaints_count%%', $entity->getComplaintsCount(), $string);
                break;
        }

        return $string;
    }

    /**
     * Заменяет сниппеты в строке на нужные данные
     *
     * @param string $title
     * @param string $type
     * @param AbstractEntity|null $entity
     * @param array $args
     *
     * @return string
     */
    private static function replaceSnippets(string $title, string $type, AbstractEntity $entity = null, array $args = [])
    {
        $entity && $title = self::replaceCustomSnippets($entity, $title);

        if (!class_exists('WPSEO_Replace_Vars')) {
            return $title;
        }

        $replacer   = new \WPSEO_Replace_Vars();
        $title      = $replacer->replace($title, $args);
        $filtersMap = [
            'title'       => 'wpseo_title',
            'description' => 'wpseo_metadesc'
        ];

        isset($filtersMap[$type]) && $title = apply_filters($filtersMap[$type], $title);

        return $title;
    }

    /**
     * Удаляте из заголовка базовые сниппеты.
     *
     * @param string $title
     *
     * @return string
     */
    private static function correctingSeoTitle(string $title)
    {
        $search = [
            '%%page%%',     // Указатель страниц
            '%%sitename%%', // Имя сайта
        ];

        $title = trim(str_replace($search, '', $title));

        if (preg_match('/.*%%sep%%$/u', $title)) {
            $title = preg_replace('/%%sep%%$/u', '', trim($title));
        }

        return $title;
    }

    public static function getPostSeoCanonical($id)
    {
        if (!class_exists('WPSEO_Meta')) {
            return '';
        }
        $canonical = \WPSEO_Meta::get_value('canonical', $id);
        // Если Yaost не отдал, то берем стандартную ссылку
        !$canonical && $canonical = get_permalink($id);

        return stripslashes(apply_filters('wpseo_canonical', $canonical));
    }

    public static function getUserSeoCanonical($uri)
    {
        return site_url($uri);
    }
}
