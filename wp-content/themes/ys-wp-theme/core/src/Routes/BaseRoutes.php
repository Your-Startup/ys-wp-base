<?php
namespace RB\Site\Routes;

class BaseRoutes
{
    /**
     * Отдаёт конфиг базового роута
     *
     * Одинаковые шаблоны добавлять рядом.
     * Не объединять регулярки (по крайней мере на стадии проектирования).
     * Для каждой страницы/раздела - свой массив.
     * example нужен на время тестов до залития на лайв
     *
     * Все API должны уметь получать информацию как по slug, так и по id
     *
     * @return array
     */
    public function getBaseRoute(): array
    {
        $routes = [
            '^affiliate-review\/?$' => [
                'template'   => 'affiliate-review/category',
                'components' => [],
                'example'    => 'affiliate-review/'
            ],

            '^affiliate-review\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'affiliate-review/page',
                'components' => [],
                'example'    => 'affiliate-review/obzor-partnerskoj-programmy-affiliates-united-william-hill/'
            ],

            '^app-reviews\/?$' => [
                'template'   => 'app-reviews/category',
                'components' => [],
                'example'    => 'app-reviews/'
            ],

            '^app-reviews\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'app-reviews/page',
                'components' => [],
                'example'    => 'app-reviews/prilozhenie-leon-dlya-android-skachat-obzor/'
            ],

            '^author\/?$' => [
                'template'   => 'user/category',
                'components' => [],
                'example'    => 'app-reviews/'
            ],

            '^author\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'user/page',
                'components' => [],
                'example'    => [
                    'author/88985/',
                    'author/nechaevrb/',
                ]
            ],

            /**
             * Жалобы
             */
            '^complaints\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'complaints/'
            ],

            '^complaints\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/page',
                'components' => [],
                'example'    => 'complaints/drugoe-wh-21-noyabrya-2014/'
            ],

            // Убедиться, что все категории по жалобам принадлежат одному и тому же шаблону
            '^complaint-kapper\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'complaint-kapper/dogovorny-e-matchi-all-in-igor-strojlov/'
            ],

            '^complaint-tag\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'complaint-tag/1xwin/'
            ],

            '^status\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'status/complaint-status-groundless/'
            ],

            '^complaint-company\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'complaint-company/10bet/'
            ],

            '^type\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'type/blokirovka-scheta/'
            ],

            '^kapper_complaints\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'kapper_complaints/'
            ],

            '^kapper_complaints\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'kapper_complaints/zhaloba-na-kappera-citibet-28-yanvarya-2016/'
            ],

            '^kapper_status\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'complaints/category',
                'components' => [],
                'example'    => 'kapper_status/complaint-status-groundless/'
            ],

            /**
             * Бонусы
             */

            // TODO .2019 ploshadka / переделать на bonuses и поставить редирект
            '^bonusy-bukmekerov\/?$' => [
                'template'   => 'bonuses/category',
                'components' => [],
                'example'    => 'bonusy-bukmekerov/'
            ],

            '^bonus-tag\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'bonuses/category-with-filter',
                'components' => [],
                'example'    => 'bonus-tag/betsupremacy/'
            ],

            '^bonusy-bukmeker\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'bonuses/category-with-filter',
                'components' => [],
                'example'    => 'bonusy-bukmeker/10bet/'
            ],

            '^bonusy-rubrika\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'bonuses/category-with-filter',
                'components' => [],
                'example'    => 'bonusy-rubrika/enhanced-odds/'
            ],

            '^bonusy-bukmekerov\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'bonuses/page',
                'components' => [],
                'example'    => 'bonusy-bukmekerov/programma-loyal-nosti-bk-leonbets/'
            ],


            /**
             * Новости
             */

            '^news\/?$' => [
                'template'   => 'news/category',
                'components' => [],
            ],

            '^news\/(?<id>[^\/]+)\/?$' => [
                'template'   => 'news/page',
                'components' => [],
                'example'    => 'news/modrich-nazval-osnovny-h+pretendentov-na-zolotoj-myach/'
            ],
        ];

        return $routes;
    }
}

