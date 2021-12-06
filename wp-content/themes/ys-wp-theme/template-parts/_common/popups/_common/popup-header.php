<?php
$socialLinks = [
    [
        'name'    => 'telegram',
        'link'    => '',
        'iconUrl' => DIST_IMAGE . '/telega.svg'
    ],
    [
        'name'    => 'tiktok',
        'link'    => '',
        'iconUrl' => DIST_IMAGE . '/TikTok.svg'
    ],
    [
        'name'    => 'vk',
        'link'    => '',
        'iconUrl' => DIST_IMAGE . '/VK.svg'
    ],
    [
        'name'    => 'youtube',
        'link'    => '',
        'iconUrl' => DIST_IMAGE . '/YT.svg'
    ],
    [
        'name'    => 'facebook',
        'link'    => '',
        'iconUrl' => DIST_IMAGE . '/FB.svg'
    ],
    [
        'name'    => 'instagram',
        'link'    => '',
        'iconUrl' => DIST_IMAGE . '/INST.svg'
    ],
]
?>

<div class="menu__header-block">
    <div class="container">
        <div class="menu__header">
            <div class="menu__link menu__logo">
                <img src="<?= DIST_IMAGE . '/menu-close.svg' ?>" alt="" class="menu__link-img js-close">
            </div>

            <a href="<?= HOME_URL ?>" class="menu__link menu-link-one">
                <img src="<?= DIST_IMAGE . '/main_logo.svg' ?>" alt="" class="menu__link-img">
            </a>

            <div class="menu__social">
                <?php foreach ($socialLinks as $social) { ?>
                    <div class="menu__social-link">
                        <a href="<?= $social['link'] ?>">
                            <img src="<?= $social['iconUrl'] ?>" alt="<?= $social['name'] ?>">
                        </a>
                    </div>
                <?php } ?>
            </div>

            <a href="#" class="personal-area js-popup-active" data-popup="login">
                <span class="menu__same-line">
                    <img src="<?= DIST_IMAGE . '/user.svg' ?>" alt="">
                </span>
                <span class="hide">Войти</span>
            </a>
        </div>
    </div>
</div>
