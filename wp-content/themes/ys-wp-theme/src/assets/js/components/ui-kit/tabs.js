export function tabs() {
    document.addEventListener('click', function (e) {
        let element = e.target;
        if (!element.classList.contains('js-tab')) {
            return;
        }

        let tabs = element.closest('.js-tabs');
        if (!tabs) {
            return;
        }

        let tabsLink = tabs.querySelectorAll('.js-tab'),
            tabsBody = tabs.querySelectorAll('.js-tab-body'),
            i        = tabsLink.indexOf(element);

        tabsLink.classList.remove('active');
        tabsBody.classList.remove('active');

        tabsLink[i].classList.add('active');
        tabsBody[i].classList.add('active');
    });
}