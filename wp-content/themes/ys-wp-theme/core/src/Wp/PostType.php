<?php

namespace YS\Core\Wp;

class PostType
{
    public function init()
    {
        $this->initPostTypes();
        $this->initTaxonomies();
        $this->initThemeSupport();
    }

    private function initPostTypes()
    {
        $postTypesPath = THEME_INC . '/configs/post-types.php';
        if (!is_file($postTypesPath)) {
            return;
        }

        $postTypes = include $postTypesPath;
        $defaults  = [
            'public'              => true,
            'exclude_from_search' => false,
            'menu_position'       => 25,
            'has_archive'         => true,
            'rewrite'             => true,
            'hierarchical'        => false,
            'show_in_nav_menus'   => true,
            'taxonomies'          => [],
            'supports'            => [
                'title',
                'editor',
                'page-attributes',
                'thumbnail',
                'excerpt',
                'comments',
                'author'
            ]

        ];

        foreach ($postTypes as $slug => $postTypeData) {
            $postTypeData = array_merge($defaults, $postTypeData);
            $this->addPostType($slug, $postTypeData, $postTypeData['title_single'], $postTypeData['title_multi']);
        }
    }

    private function initTaxonomies()
    {
        $taxonomiesPath = THEME_INC . '/configs/taxonomies.php';
        if (!is_file($taxonomiesPath)) {
            return;
        }

        $taxonomies = include $taxonomiesPath;
        $defaults   = [];

        foreach ($taxonomies as $slug => $taxData) {
            $postTypeData = array_merge($defaults, $taxData);
            $this->addTaxonomies($slug, $postTypeData);
        }
    }

    /**
     * Если в директории /fruitframe/assets/admin/icons/ есть изображение png
     * с именем <имя типа поста>.png для поста и <имя типа поста>-small.png для меню,
     * то они будут автоматически использованы в качестве иконки
     *
     * @param string $name
     * @param array $config
     * @param string $singular
     * @param string $multiple
     * @param bool $icon
     */
    private function addPostType(string $name, array $config, string $singular = 'Entry', string $multiple = 'Entries')
    {
        if (!isset($config['labels'])) {
            $config['labels'] = [
                'name'               => __($multiple, 'bmr'),
                'singular_name'      => __($singular, 'bmr'),
                'not_found'          => 'No ' . __($multiple, 'bmr') . ' Found',
                'not_found_in_trash' => 'No ' . __($multiple, 'bmr') . ' found in Trash',
                'edit_item'          => 'Edit ',
                'search_items'       => 'Search ' . __($multiple, 'bmr'),
                'view_item'          => __('Посмотреть'),
                'new_item'           => 'Новая ' . __($singular, 'bmr'),
                'add_new'            => __('Создать'),
                'add_new_item'       => 'Новая ' . __($singular, 'bmr'),
            ];
        }
        /*
        if (file_exists(FRUITFRAME_ASSETS . '/admin/icons/' . $name . '-small.png')) {
            $config['menu_icon'] = FRUITFRAME_ASSETS_URL . '/admin/icons/' . $name . '-small.png';
        }
        if (file_exists(FRUITFRAME_ASSETS . '/admin/icons/' . $name . '.png')) {
            add_action('admin_head', function () use ($name) {
                global $post_type;
                if ((@$_GET['post_type'] == $name) || ($post_type == $name)) {
                    ?>
                    <style>
                        #icon-edit {
                            background: transparent url('<?php echo FRUITFRAME_ASSETS_URL.'/admin/icons/'.$name.'.png'?>') no-repeat;
                        }
                    </style>
                    <?php
                }
            });
        }
        */
        register_post_type($name, $config);
    }

    private function addTaxonomies($name, $config)
    {
        register_taxonomy($name, $config['postTypes'], $config);
    }

    private function initThemeSupport()
    {
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        //add_image_size('medium+', 460, 287, true);\
    }
}
