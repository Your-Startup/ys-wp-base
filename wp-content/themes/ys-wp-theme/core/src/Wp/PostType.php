<?php

namespace YS\Core\Wp;

class PostType
{
    private array $postTypes = [];

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

        $this->initTemplates();
    }

    private function initTemplates()
    {
        add_action('wp_enqueue_scripts', function () {
            is_post_type_archive($this->postTypes) && $page = 'archive';
            is_singular($this->postTypes) && $page = 'single';

            if (empty($page)) {
                return;
            }

            $postType = get_query_var('post_type');

            \YS\Core\Site::getInstance()->loadAssets("$postType/$page", 'ys-page');
        });

        add_filter('archive_template_hierarchy', function ($templates) {
            $postType = str_replace(['archive-', '.php'], '', $templates[count($templates) - 2]);
            if (in_array($postType, $this->postTypes)) {
                array_unshift($templates, 'template-parts/' . $postType . '/archive.php');
            }

            return $templates;
        });

        add_filter('single_template_hierarchy', function ($templates) {
            $postType = str_replace(['single-', '.php'], '', $templates[count($templates) - 2]);
            if (in_array($postType, $this->postTypes)) {
                array_unshift($templates, 'template-parts/' . $postType . '/single.php');
            }

            return $templates;
        });
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

    private function addPostType(string $name, array $config, string $singular = 'Запись', string $multiple = 'Записи')
    {
        $config['labels'] = array_merge([
            'name'               => $multiple,
            'singular_name'      => $singular,
            'not_found'          => 'No ' . $multiple . ' Found',
            'not_found_in_trash' => 'No ' . $multiple . ' found in Trash',
            'edit_item'          => 'Edit ',
            'search_items'       => 'Search ' . $multiple,
            'view_item'          => __('Посмотреть'),
            'new_item'           => 'Новая ' . $singular,
            'add_new'            => __('Создать'),
            'add_new_item'       => 'Новая ' . $singular,
        ], $config['labels'] ?? []);

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

        $this->postTypes[] = $name;
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
