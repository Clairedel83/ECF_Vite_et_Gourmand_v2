import './styles/pages/admin.css';

// --------------------------------------------- MODIFICATION DE COMMANDE ----------------------------------------------------------
const btnForm = document.querySelector('.btn2_modifier');
const containerCta = document.querySelector('.btn_form');
const anciennesDonnees = document.querySelectorAll('.anciennes_donnees');
const nouvellesDonnees = document.querySelectorAll('.nouvelles_donnees');

if(btnForm && containerCta){
    btnForm.addEventListener('click', () => {
        // affiche les boutons liés au formulaire
        containerCta.style.display = 'flex';

        // retire le bouton modifier
        btnForm.style.display = 'none';

        // retire les anciennes données
        anciennesDonnees.forEach(element => {
            element.style.display = 'none';
        })
        
        // affiche les input pour obtenir les nouvelles données
        nouvellesDonnees.forEach(element => {
            element.style .display = 'table-cell';
        })
        
    })
}










// --------------------------------------------- ANNULATION DE COMMANDE -----------------------------------------------------------------
// Permet d'ouvrir automatiquement la modale d'annulation en cas de formulaire invalide
document.addEventListener('DOMContentLoaded', () => {
    const modale = document.getElementById('annulationCommandeModal');

    if (modale?.dataset.ouvrir === 'true') {
        bootstrap.Modal.getOrCreateInstance(modale).show();
    }
});

