<?php

const TABLE_WP_POSTS              = 'wp_posts';
const TABLE_WP_POSTMETA           = 'wp_postmeta';
const TABLE_WP_TERMS              = 'wp_terms';
const TABLE_WP_TERMMETA           = 'wp_termmeta';
const TABLE_WP_TERM_TAXONOMY      = 'wp_term_taxonomy';
const TABLE_WP_TERM_RELATIONSHIPS = 'wp_term_relationships';
const TABLE_WP_USERS              = 'wp_users';
const TABLE_WP_USERMETA           = 'wp_usermeta';
const TABLE_WP_DELETED_USERS      = 'wp_deleted_users';
const TABLE_WP_COMMENTS           = 'wp_comments';
const TABLE_WP_COMMENTMETA        = 'wp_commentmeta';

add_action('init', function() {
    $site = \YS\Core\Site::getInstance();
    $site->postType->init();

    loadFiles(__DIR__ . '/hooks');
});