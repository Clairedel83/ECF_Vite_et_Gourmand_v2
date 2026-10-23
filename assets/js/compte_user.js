const btnAfficher = document.querySelector('#lien_historique_commande');
const btnFermer = document.querySelector('#fermer_historique_commande'); 
const historiqueCommande = document.querySelector('.historique_commande');

btnAfficher.addEventListener('click', () => {
    historiqueCommande.style.display = "block";
    btnFermer.style.display = "block";
    btnAfficher.style.display = "none";
})

btnFermer.addEventListener('click', () => {
    historiqueCommande.style.display = "none";
    btnFermer.style.display = "none";
    btnAfficher.style.display = "block";    
})