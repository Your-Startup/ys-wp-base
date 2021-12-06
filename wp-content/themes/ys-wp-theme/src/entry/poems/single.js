import "../../assets/scss/pages/poems/single.scss";

import {swiperRecommended, swiperSingleMedia} from "../../assets/js/components/ui-kit/swiper";

document.addEventListener("DOMContentLoaded", function () {
    // anchorScroll(0)
    // swiperProjectSliders();
    // swiperImagePopup();
    //phoneMask();
    //scrollHeader();

    swiperSingleMedia();
    swiperRecommended();

    // Все что ниже разобрать на модули

    let infoSliderWindow = document.querySelector('.info_slider_window')

    let sliderLeft = document.querySelector('.info_slider_left')
    let sliderRight = document.querySelector('.info_slider_right')
    let carousel = document.querySelector('.info_slider_carousel')

    let carouselBlocks = document.querySelectorAll('.info_slider_carousel div')

    let slider_clicks = 0
    let windWidth = infoSliderWindow.clientWidth

    let menuBtn = document.querySelector('.menu__btn');

    let more = document.querySelector('.product_text_more');

    let productBlockBord = document.querySelectorAll('.product_block_bord');
    let productOne = document.getElementById('product_block_bord-one');
    let productTwo = document.getElementById('product_block_bord-two');
    let productText = document.getElementById('product_text');

    let popup = document.getElementById('product__popup');
    let popupContent = document.getElementById('product__popup__content');
    let contentImg = document.querySelectorAll('.product__img');
    let closed = document.getElementById('close');

    carouselBlocks.forEach(block => {
        block.style.width = (windWidth - 12) / 2 + 'px'
    })


    let goRig = () => {
        if (carouselBlocks.length - 2 > slider_clicks) {
            slider_clicks += 1
            carousel.style.left = `calc((((${windWidth + 'px'} - 12px) / 2) * ${-slider_clicks}) - ( 12px * ${slider_clicks})`
        }
    }

    let goLeft = () => {
        if (slider_clicks !== 0) {
            slider_clicks -= 1
            carousel.style.left = `calc((((${windWidth + 'px'} - 12px) / 2) * ${-slider_clicks}) - ( 12px * ${slider_clicks})`
        }
    }

    sliderRight.addEventListener('click', () => goRig())


    sliderLeft.addEventListener('click', () => goLeft())

    window.addEventListener('resize', () => {
        windWidth = infoSliderWindow.clientWidth
        carouselBlocks.forEach(block => {
            block.style.width = (windWidth - 12) / 2 + 'px'
        })
        carousel.style.left = `calc((((${windWidth + 'px'} - 12px) / 2) * ${-slider_clicks}) - ( 12px * ${slider_clicks})`
    })

    /////////////////////////////////////
    /* Спецально разделил т.к. хз как там у тебя будет разбиваться по файлам*/

    // Мой код

    productBlockBord.forEach((product) => {
        product.addEventListener('click', () => {
            productBlockBord.forEach((item) => {
                item.classList.remove('product_block_bord-active');
            });
            product.classList.add('product_block_bord-active');
        });
    });

    productOne.addEventListener('click', () => {
        productTextOne.style.display = 'block';
        productTextTwo.style.display = 'none';

        document.getElementById('product_text-hide-one').style.display = 'none';
        document.getElementById('product_text-hide-two').style.display = 'block';
        moreOne.style.display = 'block';
        moreTwo.style.display = 'none';
    });

    productTwo.addEventListener('click', () => {
        productTextOne.style.display = 'none';
        productTextTwo.style.display = 'block';

        document.getElementById('product_text-hide-one').style.display = 'block';
        document.getElementById('product_text-hide-two').style.display = 'none';
        moreOne.style.display = 'none';
        moreTwo.style.display = 'block';
    });

    contentImg.forEach((imgVid) => {
        imgVid.addEventListener('click', (e) => {
            e.preventDefault();
            let obj;
            if (imgVid.tagName == 'IMG') {
                obj = document.createElement('img');
                obj.src = imgVid.src;
                popup.style.display = 'flex';
                popupContent.appendChild(obj);
            } else if (imgVid.tagName == 'VIDEO') {
                obj = document.createElement('video');
                obj.setAttribute('controls', true);
                obj.src = imgVid.src;
                popup.style.display = 'flex';
                popupContent.appendChild(obj);
            }
        })
    })

    closed.addEventListener('click', () => {
        popupContent.innerHTML = '';
        popupContent.appendChild(closed);
        popup.style.display = 'none';
    })

    // Мой код

    moreOne.addEventListener('click', () => {
        document.getElementById('product_text-hide-one').style.display = 'block';
        document.getElementById('product_text-hide-two').style.display = 'none';
        moreOne.style.display = 'none';
        moreTwo.style.display = 'block';

    });

    moreTwo.addEventListener('click', () => {
        document.getElementById('product_text-hide-one').style.display = 'none';
        document.getElementById('product_text-hide-two').style.display = 'block';
        moreOne.style.display = 'block';
        moreTwo.style.display = 'none';
    });
});