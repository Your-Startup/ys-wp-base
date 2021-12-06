<?php
// 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'

return [
    'poems' => [
        'title_single'  => __('Стихотворение', 'poems'),
        'title_multi'   => __('Стихотворения', 'poems'),
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-book',
        'supports'      => ['title', 'editor', 'excerpt', 'thumbnail'],
        'taxonomies'    => ['poems_themes', 'poems_authors'],
        'labels' => [
            'new_item'           => 'Новое ' . __('Стихотворение', 'poems'),
            'add_new_item'       => 'Новое ' . __('Стихотворение', 'poems'),
        ]
    ],
    'transactions' => [
        'title_single'  => __('Движение средств', 'poems'),
        'title_multi'   => __('Движение средств', 'poems'),
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-money-alt',
        'supports'      => ['title'],
        'taxonomies'    => [],
        'labels' => [
            'new_item'           => 'Новая ' . __('Операция', 'poems'),
            'add_new_item'       => 'Новая ' . __('Операция', 'poems'),
        ]
    ],
    'articles' => [
        'title_single'  => __('Статья', 'poems'),
        'title_multi'   => __('Статьи', 'poems'),
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-text-page',
        'supports'      => ['title', 'editor', 'excerpt'],
        'taxonomies'    => [],
    ],
];