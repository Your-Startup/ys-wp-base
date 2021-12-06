<?php

return [
	'poems_authors' => [
		'postTypes'         => 'poems',
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'label'             => __('Авторы', 'poems'),
		'sort'              => true,
		'args'              => ['orderby' => 'term_order'],
		'show_admin_column' => true,
	],
    'poems_themes' => [
		'postTypes'         => 'poems',
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'label'             => __('Темы', 'poems'),
		'sort'              => true,
		'args'              => ['orderby' => 'term_order'],
		'show_admin_column' => true,
	],
];
