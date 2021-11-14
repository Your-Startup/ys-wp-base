<?php
// 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'

return [
    'articles' => [
        'title_single'  => __('Полезная информация', 'stroy'),
        'title_multi'   => __('Статьи', 'stroy'),
        'menu_position' => 17,
        'menu_icon'     => 'dashicons-book',
        'supports'      => ['title', 'editor', 'excerpt', 'thumbnail'],
        'taxonomies'    => ['category'],
    ],
    'our_work' => [
        'title_single'  => __('Наша работа', 'stroy'),
        'title_multi'   => __('Наши работы', 'stroy'),
        'menu_position' => 17,
        'menu_icon'     => 'dashicons-book',
        'supports'      => ['title', 'editor', 'excerpt'],
        'taxonomies'    => [],
    ],
    'slider'   => [
        'title_single'  => __('Слайд', 'stroy'),
        'title_multi'   => __('Слайдер', 'stroy'),
        'menu_position' => 17,
        'menu_icon'     => 'dashicons-format-gallery',
        'supports'      => ['title', 'editor', 'excerpt'],
        'taxonomies'    => [],
    ],
    'video'    => [
        'title_single'  => __('Видео', 'stroy'),
        'title_multi'   => __('Видео', 'stroy'),
        'menu_position' => 17,
        'menu_icon'     => 'dashicons-video-alt3',
        'supports'      => ['title', 'editor', 'excerpt'],
        'taxonomies'    => [],
    ],
    'videoR'   => [
        'title_single'  => __('Видео отзыв', 'stroy'),
        'title_multi'   => __('Видео отзывы', 'stroy'),
        'menu_position' => 17,
        'menu_icon'     => 'dashicons-video-alt3',
        'supports'      => ['title', 'editor', 'excerpt'],
        'taxonomies'    => [],
        'has_archive'   => 'reviews',
    ],
    'quiz'     => [
        'title_single'  => __('Ответ quiz', 'stroy'),
        'title_multi'   => __('Ответы quiz', 'stroy'),
        'menu_position' => 17,
        'menu_icon'     => 'dashicons-clipboard',
        'supports'      => ['title', 'editor', 'excerpt'],
        'taxonomies'    => [],
        'has_archive'   => false,
    ],
];