import "../assets/scss/pages/home.scss";

import 'owl.carousel';
import {swiperHomeReviews} from "../assets/js/components/ui-kit/swiper";
import {carousel3d} from "../assets/js/components/home/carousel-3D";

document.addEventListener("DOMContentLoaded", function () {
    swiperHomeReviews().then(() => {});
});
