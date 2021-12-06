<?php

namespace YS\Core;

use YS\Core\Entity\AbstractEntity;
use YS\Core\Repository\RepositoryInterface;
use YS\Core\Repository\Post\PostRepository;
use YS\Core\Repository\Post\PostRepositoryInterface;
use YS\Core\Repository\Term\TermRepository;
use YS\Core\Repository\User\UserRepository;
use YS\Core\Wp\PostType;

class Site
{
    static self  $instance;
    public  /*readonly*/ PostType     $postType;
    private /*readonly*/ array        $controllers = [];
    public array $currentUser = [];

    public function __construct()
    {
        $this->postType = new PostType();
        $this->loadControllers();

        // Удаление виджета "Добро пожаловать"
        remove_action('welcome_panel', 'wp_welcome_panel');

        //Определение директорий поиска шаблонов

        add_filter('show_admin_bar', '__return_false');
        add_action('wp_enqueue_scripts', [$this, 'actionLoadAssets']);

        add_filter('sanitize_title', [$this, 'filterSanitizeTitle'], 9);
        add_filter('sanitize_file_name', [$this, 'filterSanitizeTitle'], 9);
        add_action('shutdown', [$this, 'actionConvertExistingSlugs'], 9);
    }

    final public function actionLoadAssets()
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-block-style');

        $this
            ->loadAssets('runtime', 'ys-runtime', ['js'])
            ->loadAssets('base', 'ys-base',);

        wp_localize_script('ys-runtime', 'YS_SITE_DATA', [
            'url'   => HOME_URL,
            'root'  => esc_url_raw(rest_url() . 'ys/v1.0/'),
            'nonce' => wp_create_nonce('wp_rest')
        ]);
    }

    private function loadControllers(): self
    {
        $places = [
            'Site' => THEME_INC . '/src/Controller',
            'Core' => __DIR__ . '/Controller',
        ];

        foreach ($places as $place => $path) {
            $items = scandir($path) ?: [];

            foreach ($items as $item) {
                if (!is_dir($path . '/' . $item) || $item === '.' || $item === '..') {
                    continue;
                }

                if (isset($this->controllers[$item])) {
                    continue;
                }

                $class = '\YS\\' . $place . '\Controller\\' . $item . '\\' . $item . 'Controller';

                $this->controllers[$item] = (new $class())->run();
            }
        }

        return $this;
    }

    public static function getInstance(): Site
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Получает контроллер по имени
     *
     * @param string $name
     *
     * @return false|mixed
     */
    public function getController(string $name)
    {
       if (isset($this->controllers[$name])) {
           return $this->controllers[$name];
       }

       // TODO grl Нужна ошибка
        return false;
    }

    final private function getRepository(string $name, string $place = 'Site'): ?RepositoryInterface
    {
        $name = ucfirst($name);
        $path = "\YS\\$place\Repository\\$name\\{$name}Repository";

        if (class_exists($path)) {
            return new $path();
        }

        return null;
    }

    final public function getPostRepository(string $postType = 'post'): ?PostRepository
    {
        $repo = $this->getRepository($postType);
        if ($repo instanceof PostRepository) {
            return $repo;
        }

        // Вызываем базовый класс
        $repo = $this->getRepository('post', 'Core');
        if ($repo instanceof PostRepository) {
            return $repo->setPostType($postType);
        }

        return null;
    }

    final public function getTermRepository(string $taxonomy = 'term'): ?TermRepository
    {
        $repo = $this->getRepository($taxonomy);
        if ($repo instanceof TermRepository) {
            return $repo;
        }

        // Вызываем базовый класс
        $repo = $this->getRepository('term', 'Core');
        if ($repo instanceof TermRepository) {
            return $repo->setTaxonomy($taxonomy);
        }

        return null;
    }

    final public function getUserRepository(): ?UserRepository
    {
        $repo = $this->getRepository('user');
        if ($repo instanceof UserRepository) {
            return $repo;
        }

        // Вызываем базовый класс
        $repo = $this->getRepository('user', 'Core');
        if ($repo instanceof UserRepository) {
            return $repo;
        }

        return null;
    }

    /**
     * Получает данные авторизованного юзера
     *
     * @param array $fields
     *
     * @return array|AbstractEntity
     */
    public function getCurrentUser(array $fields = [])
    {
        if (!CURRENT_USER_ID) {
            return [];
        }

        if ($this->currentUser) {
            $availableFields = array_keys($this->currentUser);
            $missingFields   = array_diff($fields, $availableFields);

            // Имеются все требуемые поля
            if ($missingFields) {
                return $this->currentUser;
            }

            $fields = array_merge($availableFields, $missingFields);
        }

        return $this->currentUser = $this->getUserRepository()->find(CURRENT_USER_ID, $fields);
    }


    /**
     * Подключает стили и скрипты
     *
     * @param string $name
     * @param string|null $handle
     * @param array|string[] $types
     *
     * @return Site
     */
    public function loadAssets(string $name, string $handle = 'ys-page', array $types = ['js', 'css']) : self
    {
        $needJs  = in_array('js', $types);
        $needCss = in_array('css', $types);
        $needAll = $needJs === $needCss;

        if ($needJs || $needAll) {
            $js = "/js/$name.min.js";
            wp_enqueue_script($handle, DIST . $js, [], filemtime(DIST_PATH . $js), true);
        }

        if ($needCss || $needAll) {
            $css = "/css/$name.css";
            wp_enqueue_style($handle, DIST . $css, [], filemtime(DIST_PATH . $css));
        }

        return $this;
    }

public function filterSanitizeTitle($title) {
    global $wpdb;

    $iso9_table = [
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Ѓ' => 'G',
        'Ґ' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'YO',
        'Є' => 'YE',
        'Ж' => 'ZH',
        'З' => 'Z',
        'Ѕ' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'Ј' => 'J',
        'І' => 'I',
        'Ї' => 'YI',
        'К' => 'K',
        'Ќ' => 'K',
        'Л' => 'L',
        'Љ' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'Њ' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ў' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'TS',
        'Ч' => 'CH',
        'Џ' => 'DH',
        'Ш' => 'SH',
        'Щ' => 'SHH',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'YU',
        'Я' => 'YA',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'ѓ' => 'g',
        'ґ' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'є' => 'ye',
        'ж' => 'zh',
        'з' => 'z',
        'ѕ' => 'z',
        'и' => 'i',
        'й' => 'j',
        'ј' => 'j',
        'і' => 'i',
        'ї' => 'yi',
        'к' => 'k',
        'ќ' => 'k',
        'л' => 'l',
        'љ' => 'l',
        'м' => 'm',
        'н' => 'n',
        'њ' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ў' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'ts',
        'ч' => 'ch',
        'џ' => 'dh',
        'ш' => 'sh',
        'щ' => 'shh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya'
    ];
    $geo2lat    = [
        'ა' => 'a',
        'ბ' => 'b',
        'გ' => 'g',
        'დ' => 'd',
        'ე' => 'e',
        'ვ' => 'v',
        'ზ' => 'z',
        'თ' => 'th',
        'ი' => 'i',
        'კ' => 'k',
        'ლ' => 'l',
        'მ' => 'm',
        'ნ' => 'n',
        'ო' => 'o',
        'პ' => 'p',
        'ჟ' => 'zh',
        'რ' => 'r',
        'ს' => 's',
        'ტ' => 't',
        'უ' => 'u',
        'ფ' => 'ph',
        'ქ' => 'q',
        'ღ' => 'gh',
        'ყ' => 'qh',
        'შ' => 'sh',
        'ჩ' => 'ch',
        'ც' => 'ts',
        'ძ' => 'dz',
        'წ' => 'ts',
        'ჭ' => 'tch',
        'ხ' => 'kh',
        'ჯ' => 'j',
        'ჰ' => 'h'
    ];
    $iso9_table = array_merge($iso9_table, $geo2lat);

    $locale = get_locale();
    switch ($locale) {
        case 'bg_BG':
            $iso9_table['Щ'] = 'SHT';
            $iso9_table['щ'] = 'sht';
            $iso9_table['Ъ'] = 'A';
            $iso9_table['ъ'] = 'a';
            break;
        case 'uk':
        case 'uk_ua':
        case 'uk_UA':
            $iso9_table['И'] = 'Y';
            $iso9_table['и'] = 'y';
            break;
    }

    $is_term = false;
    $backtrace = debug_backtrace();
    foreach ($backtrace as $backtrace_entry) {
        if ($backtrace_entry['function'] == 'wp_insert_term') {
            $is_term = true;
            break;
        }
    }

    $term = $is_term ? $wpdb->get_var("SELECT slug FROM $wpdb->terms WHERE name = '$title'") : '';
    if (empty($term)) {
        $title = strtr($title, apply_filters('ctl_table', $iso9_table));
        if (function_exists('iconv')) {
            $title = iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $title);
        }
        $title = preg_replace("/[^A-Za-z0-9'_\-\.]/", '-', $title);
        $title = preg_replace('/\-+/', '-', $title);
        $title = preg_replace('/^-+/', '', $title);
        $title = preg_replace('/-+$/', '', $title);
    } else {
        $title = $term;
    }

    return $title;
}

public function actionConvertExistingSlugs() {
    global $wpdb;

    $posts = $wpdb->get_results("SELECT ID, post_name FROM $wpdb->posts WHERE post_name REGEXP('[^A-Za-z0-9\-]+') AND post_status IN ('publish', 'future', 'private')"
    );
    foreach ((array)$posts as $post) {
        $sanitized_name = $this->filterSanitizeTitle(urldecode($post->post_name));
        if ($post->post_name != $sanitized_name) {
            add_post_meta($post->ID, '_wp_old_slug', $post->post_name);
            $wpdb->update($wpdb->posts, ['post_name' => $sanitized_name], ['ID' => $post->ID]);
        }
    }

    $terms = $wpdb->get_results("SELECT term_id, slug FROM $wpdb->terms WHERE slug REGEXP('[^A-Za-z0-9\-]+') ");
    foreach ((array)$terms as $term) {
        $sanitized_slug = $this->filterSanitizeTitle(urldecode($term->slug));
        if ($term->slug != $sanitized_slug) {
            $wpdb->update($wpdb->terms, ['slug' => $sanitized_slug], ['term_id' => $term->term_id]);
        }
    }
}
}