<!doctype html>
<html <?php language_attributes() ?>>
<head>
    <!--=== META TAGS ===-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="<?php bloginfo( 'charset' ) ?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <meta name="yandex-verification" content="0668e6133000649f" />

    <!--=== LINK TAGS ===-->
    <link rel="shortcut icon" href="<?= DIST . '/images/logo.svg' ?>"/>
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name') ?> RSS2 Feed" href="<?php bloginfo('rss2_url') ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />

    <!--=== TITLE ===-->
    <title><?php wp_title() ?> - <?php bloginfo( 'name' ) ?></title>

    <!--=== WP_HEAD() ===-->
    <?php wp_head() ?>
</head>

<body <?php body_class() ?>>
<?php wp_body_open() ?>

<?php include_once TEMPLATE . '/_common/header/header.php' ?>
