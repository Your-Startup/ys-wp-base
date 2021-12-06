import {EffectFade, Lazy, Navigation, Pagination, Swiper, Thumbs} from 'swiper/swiper.esm';

Swiper.use([Pagination, EffectFade, Navigation, Lazy, Thumbs]);


export function swiperSingleMedia() {
    const swiper = new Swiper('.js-swiper-single-media', {
        slidesPerView: 2,
        spaceBetween: 8,
        slidesPerGroup: 1,
        loop: true,
        navigation: {
            nextEl: '.slider_right',
            prevEl: '.slider_left',
        },
    });

}

export function swiperRecommended() {
    const swiper = new Swiper('.js-swiper-recommended', {
        direction: 'horizontal',
        slidesPerView: 1,
        spaceBetween: 5,
        slidesPerGroup: 1,
        loop: false,
        navigation: {
            nextEl: '.js-swiper-recommended-next',
            prevEl: '.js-swiper-recommended-prev',
        },
        breakpoints: {
            1202: {
                direction: 'horizontal',
                slidesPerView: 5,
                spaceBetween: 12,
                slidesPerGroup: 3,
                loop: false,
                navigation: {
                    nextEl: '.js-swiper-recommended-next',
                    prevEl: '.js-swiper-recommended-prev',
                },
            }
        }
    });
}

export async function swiperHomeReviews() {
    // Заменить на swiper
    const reviewsCarousel = $('.reviews_blocks_carousel').owlCarousel({
        loop      : true,
        margin    : 10,
        nav       : false,
        dots      : false,
        responsive: {
            0: {
                items: 1,
            },

            800: {
                items: 2,
            },

            1200: {
                items: 3,
            }
        }
    });

    $('.reviews_blocks_right').click(function () {
        reviewsCarousel.trigger('next.owl.carousel');
    })

    $('.reviews_blocks_left').click(function () {
        reviewsCarousel.trigger('prev.owl.carousel');
    })

    $('.benefit_blocks_carousel').owlCarousel({
        loop  : true,
        margin: 10,
        nav   : false,
        dots  : true,
        items : 1,
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
