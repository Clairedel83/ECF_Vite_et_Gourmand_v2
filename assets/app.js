import './js/avis.js';

const barreNavigation = document.querySelector('.header_top');
const boutonMenu = document.querySelector('.menu_burger');
const navigation = document.querySelector('#navigation-principale');

if (barreNavigation && boutonMenu && navigation) {
    function fermerMenu() {
        barreNavigation.classList.remove('menu_ouvert');
        boutonMenu.setAttribute('aria-expanded', 'false');
        boutonMenu.setAttribute('aria-label', 'Ouvrir le menu');
        boutonMenu.querySelector('span').textContent = '☰';
    }

    boutonMenu.addEventListener('click', () => {
        const ouvert =
            boutonMenu.getAttribute('aria-expanded') === 'true';

        barreNavigation.classList.toggle('menu_ouvert', !ouvert);
        boutonMenu.setAttribute('aria-expanded', String(!ouvert));
        boutonMenu.setAttribute(
            'aria-label',
            ouvert ? 'Ouvrir le menu' : 'Fermer le menu'
        );
        boutonMenu.querySelector('span').textContent =
            ouvert ? '☰' : '×';
    });

    navigation.addEventListener('click', event => {
        if (event.target.closest('a')) {
            fermerMenu();
        }
    });

    document.addEventListener('click', event => {
        if (!barreNavigation.contains(event.target)) {
            fermerMenu();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && boutonMenu.getAttribute('aria-expanded') === 'true') 
        {
            fermerMenu();
            boutonMenu.focus();
        }
    });

    const affichageOrdinateur =
            window.matchMedia('(min-width: 1200px)');

    affichageOrdinateur.addEventListener('change', () => {
        fermerMenu();
    });
}