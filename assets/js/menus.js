// UTILISATION DES FILTRES

// Action lors du clic sur Appliquer

let btnApply = document.querySelector('.btn3_apply');

btnApply.addEventListener('click', () => {
    // récupère ce qui est indiqué/choisi par l'utilisateur
    let themeFiltre = document.getElementById('theme').value;
    let priceMinFiltre = document.getElementById('price_min').value;
    let priceMaxFiltre = document.getElementById('price_max').value;
    let nbreFiltre = document.getElementById('nbre').value;
    let regimeFiltre = document.getElementById('regime').value;
    let menus = document.querySelectorAll('.menu');

    // permet de connaitre le nombre de menu(s) affiché(s)
    let nbreResultats = 0;

    for (let menu of menus) {
        // récupère le thème de chaque menu
        const themeMenu = menu.dataset.themeId;

        // récupère le nombre minimum (de pers.) de chaque menu et transforme la chaîne de caractères en nombre
        const nbreMenu = Number(menu.dataset.nbreMin);
        
        // récupère les régimes de chaque menu et transforme les id (string) en éléments de array
        const regimeMenu = menu.dataset.regimeIds.split(',');

        // récupère le prix de chaque menu
        const priceMenu = menu.dataset.prixPerPers;

        // vérifie qu'un nombre de pers. minimum est sélectionné OU transforme la valeur en nombre et vérifie qu'il correspond au filtre : 
        const nbreCorrect = 
            nbreFiltre === "" || Number(nbreFiltre) >= nbreMenu;

        // vérifie qu'un prix min est donné OU transforme sa valeur en nombre pour vérifier qu'il corresponde au filtre:
        const priceMinCorrect =
            priceMinFiltre === "" ||  Number(priceMinFiltre) <= Number(priceMenu);

        // vérifie qu'un prix max est donné OU transforme sa valeur en nombre pour vérifier qu'il corresponde au filtre:
        const priceMaxCorrect =
            priceMaxFiltre === "" || Number(priceMaxFiltre) >= Number(priceMenu);

        // vérifie qu'un thème est sélectionné (!= 0) OU qu'il correspond au filtre :
        const themeCorrect = 
            themeFiltre === "" || themeMenu === themeFiltre;
            
        // vérifie qu'un régime est sélectionné parmi les régimes présents OU qu'il correspond au filtre :
        const regimeCorrect =
            regimeFiltre === "" ||  regimeMenu.includes(regimeFiltre);
        
        
        // vérifie que les filtres correspondent aux éléments des menus
        if (themeCorrect && regimeCorrect && nbreCorrect && priceMinCorrect && priceMaxCorrect){
            menu.style.display = 'flex';
            nbreResultats++;
            }

        else {
            menu.style.display = 'none'
        }
    }

    // permet d'afficher la section null_menu si aucun menu ne correspond aux filtres
    let nullMenu = document.querySelector('.null_menu');
    if (nbreResultats === 0 ){
        nullMenu.style.display = 'flex';
    }
    else {
        nullMenu.style.display = 'none';
    }

});

// Action lors du clic sur Réinitialiser

let btnReset = document.querySelector('.btn3_reset');

btnReset.addEventListener("click", () => {
    document.getElementById('theme').value = "";
    document.getElementById('price_min').value = "";
    document.getElementById('price_max').value = "";
    document.getElementById('nbre').value = "";
    document.getElementById('regime').value = "";
    let menus = document.querySelectorAll('.menu');
    let nullMenu = document.querySelector('.null_menu');

    for (let menu of menus) {
        menu.style.display = 'flex'
    }

    nullMenu.style.display = 'none';
});


// OUVERTURE DU MENU DETAILLE et FERMETURE DE LA CARD MENU

// fonction qui récupère la card menu et le menu détaillé correspondant à un ID
function getMenuElements(menuId) {
    let menuDetails = document.querySelector(
        // `` permettent d'insérer une variable dans une chaîne avec ${...}
        `.menu_details[data-menu-id="${menuId}"]`
    );

    let menu = document.querySelector(
        `.menu[data-menu-id="${menuId}"]`
    );

    return { menuDetails, menu };
}


let smallsBtn = document.querySelectorAll('.btn_menu');

for (let smallBtn of smallsBtn) {
// Pour chaque bouton, lorsque ce bouton est cliqué, exécute
    smallBtn.addEventListener("click", () => {
        // récupère l'id du menu grâce au bouton cliqué
        let menuId = smallBtn.dataset.menuId;

        // appelle la fonction qui permet d'obtenir le menu card + son menu détaillé
        let { menuDetails, menu } = getMenuElements(menuId);

        // affiche le menu détaillé correspondant
        menuDetails.style.display = "flex";

        // remonte la vue au niveau du menu détaillé ouvert
        menuDetails.scrollIntoView({ behavior : 'smooth'});

        // fait disparaître le menu sur lequel l'utilisateur a cliqué
        menu.style.display = "none";
    });
}

// FERMETURE DU MENU DETAILLE et REOUVERTURE DE LA CARD MENU
let btnsFermer = document.querySelectorAll('.btn_fermer');

for (let btnFermer of btnsFermer) {
// Pour chaque bouton, lorsque ce bouton est cliqué, exécute
    btnFermer.addEventListener("click", () => {
        let menuId = btnFermer.dataset.menuId;
        
        // appelle la fonction qui permet d'obtenir le menu card + son menu détaillé
        let { menuDetails, menu } = getMenuElements(menuId);

        // permet de fermer le menu détaillé ouvert
        menuDetails.style.display = "none";
        // permet de rouvrir la card menu
        menu.style.display = "flex";
    });
}
    




