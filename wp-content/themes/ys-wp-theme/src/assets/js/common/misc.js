import {closePopup} from "../components/ui-kit/popup";

/**
 * Выполняет прокрутку к ссылкам с анкорами
 * @param { number } offset Отступ от контейнера перед которым остановится скролл
 */
export function anchorScroll(offset = 80) {
    const duration = 500;

    document.addEventListener('click', (e) => {
        const element = e.target;
        if (!element.closest('a[href^="#"]')) {
            return;
        }

        const link = element.getAttribute('href');
        if (link === '#') {
            return;
        }

        e.preventDefault();

        const target = document.querySelector(link);
        if (!target) {
            return;
        }

        closePopup();

        const to                    = target.offsetTop - offset,
              start                 = window.scrollY,
              clock                 = Date.now(),
              timeout               = fn => {
                  setTimeout(fn, 15)
              },
              requestAnimationFrame =
                  window.requestAnimationFrame || window.webkitRequestAnimationFrame || timeout,
              step                  = () => {
                  const elapsed  = Date.now() - clock,
                        position = elapsed > duration
                            ? to
                            : start + (to - start) * (-(elapsed / duration) * (elapsed / duration - 2));

                  window.scrollTo(0, position);

                  if (elapsed < duration) {
                      requestAnimationFrame(step);
                  }
              };

        history.pushState(null, null, link);
        step();
    });
}

function setCursorPosition(pos, elem) {
    elem.focus();

    if (elem.setSelectionRange) {
        elem.setSelectionRange(pos, pos)
    } else if (elem.createTextRange) {
        let range = elem.createTextRange()
            .collapse(true);

        range.moveEnd('character', pos);
        range.moveStart('character', pos);
        range.select()
    }
}

export function phoneMask() {
    document.addEventListener('input', function (e) {
        let element = e.target;
        if (!element.classList.contains('js-phone-mask')) {
            return;
        }

        let matrix = '+7 (___) ___-__-__',
            i      = 0,
            def    = matrix.replace(/\D/g, ""),
            val    = e.target.value.replace(/\D/g, "");

        def.length >= val.length && (val = def);
        e.target.value = matrix.replace(/./g, function (a) {
            return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" : a
        });

        e.type === 'blur'
            ? e.target.value.length === 2 && (e.target.value = "")
            : setCursorPosition(e.target.value.length, e.target);
    });
}


export function scrollHeader() {
    document.addEventListener('scroll', function (e) {
        let header = document.querySelector('.js-header');
        if (!header) {
            return;
        }

        let isScroll = header.classList.contains('is-scroll');

        window.scrollY > 0
            ? !isScroll && header.classList.add('is-scroll')
            : isScroll && header.classList.remove('is-scroll');
    });
}
