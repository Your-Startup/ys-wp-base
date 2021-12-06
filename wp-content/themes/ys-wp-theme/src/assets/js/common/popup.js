import {Cookies} from "../../libs/extendedjs/cookies";

export function closeCookiesPopup() {
    document.addEventListener('click', (e) => {
        const element = e.target;

        if (!element.closest('.js-cookie .js-cookie-close')) {
            return;
        }

        Cookies.set('accept_cookies', 1, null, '/');
        document.querySelector('.js-cookie').remove();
    });
}