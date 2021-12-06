<?php
?>
<div class="popup-inner popup-menu is-hidden js-popup-inner js-popup-fullscreen" data-popup="reset-password">
    <div class="popup-close icon-close is-mobile js-close"></div>

    <div class="popup-content menu">
        <?php require TEMPLATE . '/_common/popups/_common/popup-header.php' ?>

        <div class="container">
            <form class="form password__form js-reset-password-form">
                <h2 class="password__heading">Забыли пароль?</h2>
                <p class="password__text">Не волнуйтесь, мы отправим вам сообщение для сброса пароля.</p>
                <label>
                    <input autocomplete="off" type="text" name="email" placeholder="Электронный адрес" class="password__input">
                </label>
                <input type="submit" value="ПРОДОЛЖИТЬ" class="password__submit">
            </form>
        </div>

        <?php require TEMPLATE . '/_common/popups/_common/popup-footer.php' ?>
    </div>
</div>


