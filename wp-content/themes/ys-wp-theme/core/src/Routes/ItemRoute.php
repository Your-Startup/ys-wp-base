<?php
namespace YS\Core\Routes;

class ItemRoute
{
    /**
     * @param string $slug
     *
     * @return string
     */
    public function getUserRoute($slug): string
    {
        return '/author/' . $slug . '/';
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public function getFaceRoute($slug): string
    {
        return '/person/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getNewsRoute($slug): string
    {
        return '/news/' . $slug . '/';
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getNewsCategoriesRoute($id): string
    {
        $args = [
            'format' => 'slug',
            'link'   => false,
        ];

        return '/news/categories/' . get_term_parents_list($id, 'news_category', $args);
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public function getTextBookCategoriesRoute($slug): string
    {
        return '/wiki-category/textbooks/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getTagsRoute($slug): string
    {
        return '/tag/' . $slug . '/';
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getWikiCategoriesRoute($id): string
    {
        $args = [
            'format' => 'slug',
            'link'   => false,
        ];

        return '/wiki-category/' . get_term_parents_list($id, 'wiki_category', $args);
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getWikiRoute($slug): string
    {
        return '/wiki/' . $slug . '/';
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public function getQuestionRoute($slug): string
    {
        return '/questions/' . $slug . '/';
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public function getQuestionsCategoryRoute($slug): string
    {
        return '/questions_category/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getComplaintRoute($slug): string
    {
        return '/complaints/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getComplaintTypeRoute($slug): string
    {
        return '/type/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getComplaintTagRoute($slug): string
    {
        return '/complaint-company/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getHandicapperComplaintRoute($slug): string
    {
        return '/kapper_complaints/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getBonusRoute($slug): string
    {
        return '/bonusy-bukmekerov/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getBonusCategoryRoute($slug): string
    {
        return '/bonusy-rubrika/' . $slug . '/';
    }

    public function getBonusBookmakerRoute($slug): string
    {
        return '/bonusy-bukmeker/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getForecastRoute($slug): string
    {
        return '/tips/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getForecastsCategoriesRoute($slug): string
    {
        return '/tip-category/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getForecastsTypesRoute($slug): string
    {
        return '/tip/sport-types/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getAppRoute($slug): string
    {
        return '/app-reviews/' . $slug . '/';
    }

    public function getBookmakerRoute(string $slug): string
    {
        return '/review/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getForkScannerRoute($slug): string
    {
        return '/forks-scanners/' . $slug . '/';
    }

    public function getSupportedLanguagesRoute($slug): string
    {
        return '/supported_languages/' . $slug . '/';
    }

    public function getOrganizationRoute($slug): string
    {
        return '/organizations/' . $slug . '/';
    }

    public function getSoftwareRoute($slug): string
    {
        return '/softwares/' . $slug . '/';
    }

    public function getLicenceRoute($slug): string
    {
        return '/gambling_licences/' . $slug . '/';
    }

    public function getForkScannerCurrencyRoute($slug): string
    {
        return '/fs-supported-currencies/' . $slug . '/';
    }


    public function getSupportedCurrencyRoute($slug): string
    {
        return '/supported_currencies/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать post_name из wp_posts)
     *
     * @return string
     */
    public function getPaymentSystemRoute($slug): string
    {
        return '/payment-system-review/' . $slug . '/';
    }

    public function getPaymentSystemSupportedLanguageRoute($slug): string
    {
        return '/ps-supported-languages/' . $slug . '/';
    }

    public function getPaymentSystemCurrencyRoute($slug): string
    {
        return '/ps-supported-currencies/' . $slug . '/';
    }

    /**
     * Возвращает ссылку на страницу со всеми отзывами
     *
     * @param string $uri Сформированная относительная ссылка на основную запись с отзывами
     *
     * @return string
     */
    public function getAllFeedbacksPage(string $uri): string
    {
        return trailingslashit(untrailingslashit($uri) . '/' . \BmrFeedbacks::ALL_FEEDBACKS_SLUG);
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getEventRoute($slug): string
    {
        return '/events/' . $slug . '/';
    }

    /**
     * @param string $slug
     * (Передать slug из wp_terms)
     *
     * @return string
     */
    public function getTournamentRoute($slug): string
    {
        return '/tournaments/' . $slug . '/';
    }

    public function getContestRoute($slug): string
    {
        return '/contests/' . $slug . '/';
    }

    /**
     * Делает ссылку относительной
     *
     * @param string $uri Ссылка
     *
     * @return string
     */
    public function makeUriRelative(string $uri): string
    {
        return wp_make_link_relative($uri);
    }
}
