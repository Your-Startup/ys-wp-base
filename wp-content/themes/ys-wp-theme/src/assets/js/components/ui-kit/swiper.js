import {EffectFade, Lazy, Navigation, Pagination, Swiper, Thumbs} from 'swiper/js/swiper.esm';

Swiper.use([Pagination, EffectFade, Navigation, Lazy, Thumbs]);

export function swiperProjectSliders() {
    new Swiper('.js-project-slider', {
        slidesPerView: 1,
        speed        : 500,
        simulateTouch: false,
        spaceBetween : 15,
        thumbs       : {
            swiper: new Swiper('.js-project-slider-thumbs', {
                spaceBetween         : 8,
                slidesPerView        : 3,
                freeMode             : true,
                watchSlidesVisibility: true,
                watchSlidesProgress  : true,
                navigation           : {
                    nextEl: '.js-project-slider-next',
                    prevEl: '.js-project-slider-prev',
                }
            })
        },
        preloadImages: false,
        lazy         : {
            loadOnTransitionStart: true,
            //preloaderClass: 'true',
        },
    });
}

export function swiperImagePopup() {
    let swiper = new Swiper('.js-popup-slider', {
        slidesPerView: 1,
        speed        : 500,
        spaceBetween : 20,
        navigation   : {
            nextEl: '.js-project-slider-next',
            prevEl: '.js-project-slider-prev',
        },
        preloadImages: false,
        lazy         : {
            loadOnTransitionStart: true,
            //preloaderClass: 'true',
        },
    });

    document.addEventListener('click', function (e) {
        let element = e.target;

        element = element.closest('.js-popup-active');

        if (!element || !element.dataset.image) {
            return;
        }

        swiper.activeIndex = Array.from(element.parentNode.children).indexOf(element);
        swiper.update();
    });
}
