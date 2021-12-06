import "../assets/scss/pages/home.scss";

//import {anchorScroll, phoneMask, scrollHeader} from "../../assets/js/common/misc";

//import {swiperProjectSliders, swiperImagePopup} from "../../assets/js/components/ui-kit/swiper";

import 'owl.carousel';
import {swiperHomeReviews} from "../assets/js/components/ui-kit/swiper";
import {carousel3d} from "../assets/js/components/home/carousel-3D";

document.addEventListener("DOMContentLoaded", function () {
    // anchorScroll(0)
    // swiperProjectSliders();
    // swiperImagePopup();
    // phoneMask();
    // scrollHeader();

    carousel3d();
    swiperHomeReviews().then(() => {});
});
