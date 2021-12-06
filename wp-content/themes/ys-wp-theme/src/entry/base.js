import "../assets/scss/pages/base.scss";

import "@exjs";

import {authForm, registrationForm, resetPasswordForm} from "../assets/js/common/forms";
import {popupJs} from "../assets/js/components/ui-kit/popup";
import {openMobileMenu} from "../assets/js/common/menu";
import {closeCookiesPopup} from "../assets/js/common/popup";
import {phoneMask} from "../assets/js/common/misc";

document.addEventListener("DOMContentLoaded", function () {
    phoneMask();
    popupJs();

    openMobileMenu();
    closeCookiesPopup();

    authForm();
    resetPasswordForm();
    registrationForm();
});
