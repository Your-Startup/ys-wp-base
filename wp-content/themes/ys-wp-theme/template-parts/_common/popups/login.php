<?php
/**
 * @var $popupActive
 */

$isHidden = $popupActive === 'login' ? '' : ' is-hidden';
?>
<div class="popup-inner popup-menu<?= $isHidden ?> js-popup-inner js-popup-fullscreen" data-popup="login">
    <div class="popup-close icon-close is-mobile js-close"></div>

    <div class="popup-content menu">
        <?php require TEMPLATE . '/_common/popups/_common/popup-header.php' ?>

        <div class="container">
            <h2 class="sign-in__heading">Вход в учетную запись</h2>
        </div>

        <div class="container">
            <form class="form sign-in__form js-auth-form">
                <div class="sign-in__wrapper">
                    <button class="sign-in__btn">
                        <span class="sign-in__same-line">
                            <img class="sign-in__img" src="<?= DIST_IMAGE . '/google.svg' ?>" alt="">
                        </span>Войти через Google
                    </button>
                    <button class="sign-in__btn">
                        <span class="sign-in__same-line">
                            <img class="sign-in__img" src="<?= DIST_IMAGE . '/facebook.svg' ?>" alt="">
                        </span>Войти
                        через Facebook
                    </button>
                    <button class="sign-in__btn">
                        <span class="sign-in__same-line">
                            <img class="sign-in__img" src="<?= DIST_IMAGE . '/vkontakte.svg' ?>" alt="">
                        </span>Войти
                        через Вконтакте
                    </button>
                </div>

                <div class="sign-in__or">
                    <div class="stick"></div>
                    <div class="or">или</div>
                    <div class="stick"></div>
                </div>

                <h3 class="sign-in__heading-low">Электронный адрес / номер телефона</h3>
                <label class="sign-in__label">
                    <input autocomplete="off"
                           type="text"
                           name="name"
                           class="sign-in__input"
                           placeholder="Электронный адрес / номер телефона"
                    >
                </label>

                <h3 class="sign-in__heading-low">Пароль</h3>
                <label class="sign-in__label">
                    <input autocomplete="off"
                           type="password"
                           name="password"
                           class="sign-in__input sign-in__input-last"
                           placeholder=". . . . . . . . . . . . . ."
                    >
                </label>
                <div class="sign-in__label">
                    <input type="submit" name="submit" value="ВОЙТИ" class="sign-in__submit">
                </div>

                <a href="#" class="sign-in__text js-popup-active" data-popup="reset-password">Забыли пароль?</a>
                <p class="sign-in__content">
                    <span class="hide">Впервые на сайте?</span>
                    <span class="same-line">
                        <a href="#" class="sign-in__link js-popup-active" data-popup="registration">Зарегистрируйтесь</a>
                    </span>
                </p>
            </form>
        </div>

        <?php require TEMPLATE . '/_common/popups/_common/popup-footer.php' ?>
    </div>
</div>


