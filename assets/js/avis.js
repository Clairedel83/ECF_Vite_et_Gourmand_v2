// --------------------------------------------- CARROUSEL DES AVIS ----------------------------------------------------------

const carouselAvis = document.querySelector('#carousel_avis');
const boutonPrecedent = document.querySelector('#avis_precedent');
const boutonSuivant = document.querySelector('#avis_suivant');
const reperesAvis = document.querySelectorAll('.avis_repere');

if (carouselAvis && carouselAvis.querySelector('.avis')) {

    // Calcule le déplacement : largeur d'une carte + espace entre deux cartes.
    function calculerDeplacement() {
        const carte = carouselAvis.querySelector('.avis');
        const styles = window.getComputedStyle(carouselAvis);
        const espace = parseFloat(styles.columnGap);

        return carte.getBoundingClientRect().width + espace;
    }

    // Respecte la préférence de réduction des animations.
    let comportement = 'smooth';

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        comportement = 'auto';
    }

    // Actualise les flèches et le point correspondant à l'avis visible.
    function actualiserNavigation() {
        const positionMaximale = carouselAvis.scrollWidth - carouselAvis.clientWidth;

        if (boutonPrecedent && boutonSuivant) {
            boutonPrecedent.disabled = carouselAvis.scrollLeft <= 1;
            boutonSuivant.disabled = carouselAvis.scrollLeft >= positionMaximale - 1;
        }

        // Sur mobile, chaque déplacement correspond à une carte entière.
        const indexActif = Math.round(
            carouselAvis.scrollLeft / calculerDeplacement()
        );

        reperesAvis.forEach(function (repere, index) {
            if (index === indexActif) {
                repere.classList.add('actif');
                repere.setAttribute('aria-current', 'true');
            } else {
                repere.classList.remove('actif');
                repere.setAttribute('aria-current', 'false');
            }
        });
    }

    // --------------------------- FLÈCHES ORDINATEUR -----------------------------------

    if (boutonPrecedent && boutonSuivant) {
        boutonPrecedent.addEventListener('click', function () {
            carouselAvis.scrollBy({
                left: -calculerDeplacement(),
                behavior: comportement,
            });
        });

        boutonSuivant.addEventListener('click', function () {
            carouselAvis.scrollBy({
                left: calculerDeplacement(),
                behavior: comportement,
            });
        });
    }

    // -------------------------- REPÈRES MOBILE ET TABLETTE ------------------------------

    reperesAvis.forEach(function (repere) {
        repere.addEventListener('click', function () {

            // Récupère la position de l'avis associée au bouton.
            const index = Number(repere.dataset.index);

            // Se place directement sur cet avis.
            carouselAvis.scrollTo({
                left: index * calculerDeplacement(),
                behavior: comportement,
            });
        });
    });

    // Suit le glissement tactile et les déplacements avec les boutons.
    carouselAvis.addEventListener('scroll', actualiserNavigation);

    // Recalcule la navigation lorsque la largeur de l'écran change.
    window.addEventListener('resize', actualiserNavigation);

    actualiserNavigation();
}