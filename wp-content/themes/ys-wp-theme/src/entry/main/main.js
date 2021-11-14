import "../../assets/scss/pages/main.scss";

import "@exjs";

import {anchorScroll, form, getMore, phoneMask, scrollHeader, tabs} from "../../assets/js/common/misc";
import {quiz} from "../../assets/js/main/quiz";
import { popupJs } from '../../assets/js/components/ui-kit/popup.js';
import {swiperProjectSliders, swiperImagePopup} from "../../assets/js/components/ui-kit/swiper";


document.addEventListener("DOMContentLoaded", function() {
    anchorScroll(0)
    quiz();
    swiperProjectSliders();
    tabs();
    getMore();
    popupJs();
    swiperImagePopup();
    form();
    phoneMask();
    scrollHeader();
});
