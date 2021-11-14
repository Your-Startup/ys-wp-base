export function accordionJs() {
    document.addEventListener('click', function (e) {
        let target    = e.target,
            accordion = target.closest('.js-accordion'),
            element   = target.closest('.js-accordion-active');

        if (!accordion || !element){
            return;
        }

        let wrapper = accordion.querySelector('.js-accordion-wrapper');
        if (!wrapper){
            return;
        }

        let elements = accordion.querySelectorAll('.js-accordion-active'),
            inners   = accordion.querySelectorAll('.js-accordion-inner'),
            index    = elements.indexOf(element);

        if (!inners[index]){
            return;
        }

        inners.classList.remove('active');
        elements.classList.remove('active');

        let panel = inners[index];

        inners.forEach(function (el) {
            el.style.maxHeight = null;
        });

        panel.style.maxHeight = panel.scrollHeight + "px";
        panel.classList.add('active');
        element.classList.add('active');
    });
}
