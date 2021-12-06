import 'jquery-ui/ui/widgets/accordion';

export function openMobileMenu() {
    document.addEventListener('click', (e) => {
        const element = e.target;
        if (!element.closest('.menu__btn')) {
            return;
        }

        const specialMenu = document.querySelector('.special__menu');
        if (!specialMenu) {
            return;
        }

        element.classList.toggle('open-menu');

        if (element.classList.contains('open-menu')) {
            specialMenu.style.display = 'block';
            element.innerHTML         = `<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M24.853 1.14697L1.00002 25" stroke="#276F97" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M1.14746 1L25.0005 24.853" stroke="#276F97" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>`;

            return;
        }

        specialMenu.style.display = 'none';
        element.innerHTML         = `<svg width="32" height="24" viewbox="0 0 32 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M1 11.9092H31" stroke="#276F97" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M1 1H31" stroke="#276F97" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M1 22.8184H31" stroke="#276F97" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>`;
    });


    $("#accordion").accordion({
        heightStyle: 'content',
        collapsible: true,
        active: 0,
    });
}
