<?php

return [
	'forbidden_countries' => [
		'postTypes'         => 'games',
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'label'             => __('Категории', 'grl'),
		'sort'              => true,
		'args'              => ['orderby' => 'term_order'],
		'rewrite'           => ['slug' => 'forbidden_countries'],
		'query_var'         => 'forbidden_countries',
		'show_admin_column' => true,
	]
];
