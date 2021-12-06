<?php
?>
<div class="popup-inner popup-menu is-hidden js-popup-inner js-popup-fullscreen" data-popup="registration">
    <div class="popup-close icon-close is-mobile js-close"></div>

    <div class="popup-content menu">
        <?php require TEMPLATE . '/_common/popups/_common/popup-header.php' ?>

        <div class="container">
            <form class="form registration__form js-registration-form">
                <label>
                    <input autocomplete="off" type="text" name="full_name" class="registration__input" placeholder="Имя и фамилия">
                </label>
                <label>
                    <input autocomplete="off" type="text" name="login" class="registration__input" placeholder="Никнейм">
                </label>
                <label>
                    <input autocomplete="off" type="text" name="email" class="registration__input" placeholder="Почта">
                </label>
                <label>
                    <input autocomplete="off" type="tel" name="phone" class="registration__input js-phone-mask" placeholder="Телефон">
                </label>
                <input type="submit" name="submit" class="registration__btn" value="ЗАРЕГИСТРИРОВАТЬСЯ">
            </form>
        </div>

        <?php require TEMPLATE . '/_common/popups/_common/popup-footer.php' ?>
    </div>
</div>


