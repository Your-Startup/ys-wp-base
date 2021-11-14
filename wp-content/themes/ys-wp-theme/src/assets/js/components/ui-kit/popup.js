/**
 * Выводит попап
 */
export function popupJs() {
    document.addEventListener('click', function (e) {
        let element   = e.target,
            open, close;

        close   = element.classList.contains('js-close');
        element = element.closest('.js-popup-active');
        open    = element && element.dataset.popup;

        if (!open && !close) {
            return;
        }

        openPopupInAction(open);
        close && closePopup();

        open === 'image' && openPopupImage(element);
    });
}

/**
 * Выводит нужны попап
 *
 * @param popupDataSelector
 */
export function openPopupInAction(popupDataSelector) {
    if (!popupDataSelector) {
        return;
    }

    let popup       = document.querySelector('.js-popup-wrapper'),
        popupInner  = document.querySelector(`.js-popup-inner[data-popup="${popupDataSelector}"]`),
        popupInners = document.querySelectorAll(`.js-popup-inner`),
        html        = document.documentElement;

    popup.classList.remove('is-hidden');
    popupInners.classList.add('is-hidden');
    html.classList.add('overflow-hidden');

    if (popupInner.classList.contains('js-overlay-fix')) {
     //   popup.classList.add('js-stop-closing')
    }

    if (popupInner.classList.contains('js-popup-fullscreen')) {
        popup.classList.add('popup-fullscreen')
    }

    popupInner.classList.remove('is-hidden');
}

/**
 * Закрывает все попапы
 */
export function closePopup() {
    let popup       = document.querySelector('.js-popup-wrapper'),
        popupInners = document.querySelectorAll(`.js-popup-inner`),
        html        = document.documentElement;

    popup.classList.add('is-hidden');
    popupInners.classList.add('is-hidden');
    html.classList.remove('overflow-hidden');
    popup.classList.remove('popup-fullscreen');
   // popup.classList.remove('js-stop-closing');
}

/**
 * Добовляет фото в попап
 *
 * @param element
 */
function openPopupImage(element) {
    if (!element) {
        return;
    }

    let popupInner = document.querySelector(`.js-popup-inner[data-popup="image"]`),
        wrapper    = popupInner.querySelector('.js-popup-slides'),
        slide      = popupInner.querySelector('.js-popup-image .js-popup-slide'),
        img        = slide.querySelector('img');

    wrapper.innerHTML = '';

    element.parentNode.querySelectorAll(`[data-image]`).forEach((el) => {
        img.src = el.dataset.image
        wrapper.append(slide.cloneNode(true));
    });
}


