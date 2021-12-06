import "../../assets/scss/pages/page/voprosy-i-otvety.scss";

document.addEventListener("DOMContentLoaded", function () {
    let accordionItem = document.querySelectorAll('.faq__accordion__item');

    accordionItem.forEach((item) => {
        item.addEventListener('click', () => {
            let trigger = item.classList.contains('faq__accordion__item-active');
            accordionItem.forEach((object) => {
                object.classList.remove('faq__accordion__item-active');
                object.querySelector('.accordion__heading').classList.remove('accordion__heading-active');
            });
            if (!trigger) {
                item.classList.add('faq__accordion__item-active');
                item.querySelector('.accordion__heading').classList.add('accordion__heading-active');
            }
        });
    });

    $("#accordion-faq").accordion({
        heightStyle: 'content',
        collapsible: true,
        active: 0,
    });

});
