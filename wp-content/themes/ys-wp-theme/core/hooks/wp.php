<?php
add_action('wp_dashboard_setup', function () {
    $dash_side   = &$GLOBALS['wp_meta_boxes']['dashboard']['side']['core'];
    $dash_normal = &$GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'];

    unset($dash_side['dashboard_quick_press']);       // Быстрая публикация
    unset($dash_side['dashboard_recent_drafts']);     // Последние черновики
    unset($dash_side['dashboard_primary']);           // Блог WordPress
    unset($dash_side['dashboard_secondary']);         // Другие Новости WordPress

    unset($dash_normal['dashboard_incoming_links']);  // Входящие ссылки
    unset($dash_normal['dashboard_right_now']);       // Прямо сейчас
    unset($dash_normal['dashboard_recent_comments']); // Последние комментарии
    unset($dash_normal['dashboard_plugins']);         // Последние Плагины

    unset($dash_normal['dashboard_activity']);        // Активность
});

add_action('widgets_init', function () {
    unregister_widget('WP_Widget_Archives'); // Архивы
    unregister_widget('WP_Widget_Calendar'); // Календарь
    unregister_widget('WP_Widget_Categories'); // Рубрики
    unregister_widget('WP_Widget_Meta'); // Мета
    unregister_widget('WP_Widget_Pages'); // Страницы
    unregister_widget('WP_Widget_Recent_Comments'); // Свежие комментарии
    unregister_widget('WP_Widget_Recent_Posts'); // Свежие записи
    unregister_widget('WP_Widget_RSS'); // RSS
    unregister_widget('WP_Widget_Search'); // Поиск
    unregister_widget('WP_Widget_Tag_Cloud'); // Облако меток
    unregister_widget('WP_Widget_Text'); // Текст
    unregister_widget('WP_Nav_Menu_Widget'); // Произвольное меню
}, 20);

add_action('init', function () {
    /*--- REMOVE GENERATOR META TAG ---*/
    remove_action('wp_head', 'feed_links', 2); // Удаляет ссылки RSS-лент записи и комментариев
    remove_action('wp_head', 'feed_links_extra', 3); // Удаляет ссылки RSS-лент категорий и архивов
    remove_action('wp_head', 'rsd_link'); // Удаляет RSD ссылку для удаленной публикации
    remove_action('wp_head', 'wlwmanifest_link'); // Удаляет ссылку Windows для Live Writer
    remove_action('wp_head', 'wp_shortlink_wp_head', 10); // Удаляет короткую ссылку
    remove_action('wp_head', 'wp_generator'); // Удаляет информацию о версии WordPress
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head',
        10); // Удаляет ссылки на предыдущую и следующую статьи
    // отключение WordPress REST API
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action( 'template_redirect', 'rest_output_link_header', 11);

    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
});

