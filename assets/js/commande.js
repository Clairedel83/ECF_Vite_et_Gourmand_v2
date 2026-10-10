// SOUS TOTAL
// Actualisation du sous-total en fonction du nombre de convives

// Récupère les éléments nécessaires au calcul
const inputNbreConvives = document.querySelector('#nbre_convives');
const prixPerPers = document.querySelector('#prixPerPers');
const sousTotal = document.querySelector('#sous_total');
const nbre_min = document.querySelector('#nbre_min');
const nbrePromo = Number(nbre_min.textContent) + 5;


// Calcule et affiche le sous-total
function calculSousTotal() {
    // Récupère le nombre de convives (input) et transforme le en number
    const nbreConvives = Number(inputNbreConvives.value);

    // Récupère le prix par personne
    const prix = Number(prixPerPers.textContent);

    // Calcule le sous-total
    let total = nbreConvives * prix;

    // Applique une réduction de 10 % à partir de 5 convives supplémentaires 
    if(nbreConvives >= nbrePromo){
        total = (nbreConvives * prix) - (0.1 * nbreConvives * prix);
    };

    // Affiche le sous-total avec deux chiffres après la virgule
    sousTotal.textContent = total.toFixed(2);
}


// Recalcule à chaque modification du nombre de convives
inputNbreConvives.addEventListener('input', () => {
    calculSousTotal();
    calculTotal();
});


// LIVRAISON
// Modification de l'adresse de livraison : 
    // déplie le formulaire après clic
    // calcule les frais de livraison
    // comportement en cas d'erreur

const btnModifierAdresse = document.querySelectorAll('.modifier_adresse');
const adresseUser = document.querySelector('.livraison_adresse');
const adresseModifiee = document.querySelector('.livraison_adresse_modifiee');

const nouvelleAdresse = document.querySelector('.livraison_nouvelle_adresse');
const formAdresse = document.querySelector('#adresse_livraison_form');

const inputNom = document.querySelector('#adresse_livraison_nom');
const inputPrenom = document.querySelector('#adresse_livraison_prenom');
const inputAdresse = document.querySelector('#adresse_livraison_adresse_postale');
const inputPostal = document.querySelector('#adresse_livraison_code_postal');
const inputVille = document.querySelector('#adresse_livraison_ville');


const nomNew = document.querySelector('#nom_new');
const adresseNew = document.querySelector('#adresse_new');
const villeNew = document.querySelector('#ville_new');

const formAdresseChoisie = document.querySelector('#form_adresse_choisie');
const formAdresseGoogle = document.querySelector('#form_adresse_google');
const formVilleLivraison = document.querySelector('#form_ville_livraison');

const prixLivraison = document.querySelector('#prix_livraison');
const erreurLivraison = document.querySelector('#erreur_livraison');
const btnRetryLivraison = document.querySelector('#btn_retry_livraison');
const btnValiderCommande = document.querySelector('#btn_valider_commande');

// ajoute l'évènement aux deux boutons dont la class est modifier_adresse
btnModifierAdresse.forEach(btn => {
    btn.addEventListener('click', (event) => {
        // ne pas recharger la page
        event.preventDefault();

        // retire l'adresse de l'utilisateur
        adresseUser.style.display = 'none';

        // retire l'adresse précédemment modifiée
        nouvelleAdresse.style.display = 'none';

        // déplie le formulaire de modification d'adresse
        adresseModifiee.style.display = 'block';
    });
});

// Modification de l'adresse de livraison : 
formAdresse.addEventListener('submit', (event) => {
    // ne pas recharger la page
    event.preventDefault();

    // récupère les données saisies par l'utilisateur dans le formulaire et les affiche dans la nouvelle adresse
    nomNew.textContent = inputNom.value + ' ' + inputPrenom.value;
    adresseNew.textContent = inputAdresse.value;
    villeNew.textContent = inputPostal.value + ' ' + inputVille.value;

    // enregistre la nouvelle adresse qui sera envoyée lors de la commande
    formAdresseChoisie.value = 
        inputNom.value + ' ' +
        inputPrenom.value + ' - ' +
        inputAdresse.value + ' - ' +
        inputPostal.value + ' ' +
        inputVille.value;

    // enregistre la nouvelle adresse au format google (sans le nom et prénom)
    formAdresseGoogle.value =
        inputAdresse.value + ', ' +
        inputPostal.value + ' ' +
        inputVille.value;
    
    // enregistre la ville seule pour calculer le prix de livraison (0€ si Bordeaux)
    formVilleLivraison.value = inputVille.value;

    // calcule le prix de livraison
    calculLivraison();

    // retire l'affichage du formulaire
    adresseModifiee.style.display = 'none';

    // déplie l'affichage de la nouvelle adresse
    nouvelleAdresse.style.display = 'block';
});


// Actualisation du prix de livraison en fonction de la ville de livraison
async function calculLivraison() {
    // Récupère l'adresse et la ville de livraison
    const adresse = formAdresseGoogle.value;
    const ville = formVilleLivraison.value;

    // Désactive la validation pendant le calcul
    btnValiderCommande.disabled = true;

    // Efface l'ancien prix pour ne pas conserver un tarif incorrect
    prixLivraison.textContent = '--';

    // Actualise le total pendant le calcul
    calculTotal();

    // Masque le message et le bouton d'erreur
    erreurLivraison.style.display = 'none';
    btnRetryLivraison.style.display = 'none';

    // Sécurité ajoutée en cas d'erreur de l'API Google Routes
    try{
        // prépare les données à envoyer à Symfony
        // FormData permettra d'organiser les données comme un formulaire HTML
        // append ajoute les données au formulaire
        const donnees = new FormData();
        donnees.append('adresse', adresse);
        donnees.append('ville', ville);
    
        // effectue une requête HTTP à Symfony et permet d'accéder au controller CalculPrixLivraison
        const response = await fetch('/calcul/prix/livraison', {
            method: 'POST',
            // append attribue la donnée dans le "formulaire"
            body: donnees
        });

        // Vérifie que Symfony a répondu correctement (= erreur de l'API)
        if (!response.ok) {
            throw new Error('Erreur lors du calcul de livraison');
        }

        // Récupère les données JSON envoyées par Symfony (= prix calculé)
        const data = await response.json();
        console.log(data);

        // Affiche le prix de livraison avec deux décimales
        prixLivraison.textContent = data.prixLivraison.toFixed(2);

        // Actualise le prix total de la commande
        calculTotal();

        // Autorise la validation de la commande
        verifierCommande();

    } catch(error) {
        // Affiche l'erreur dans la console
        console.error('Erreur lors du calcul de livraison :', error);

        // Affiche le message d'erreur et le bouton Réessayer
        erreurLivraison.style.display = 'block';
        btnRetryLivraison.style.display = 'block';

        // Empêche la validation de la commande
        btnValiderCommande.disabled = true;
    }
}

// Relance le calcul de livraison lorsque l'utilisateur clique sur Réessayer
btnRetryLivraison.addEventListener('click', () => {
    calculLivraison();
});


// MATERIEL
// Actualisation du prix si location de matériel

const choixMateriel = document.querySelectorAll('input[name="materiel"]');
const prixMateriel = document.querySelectorAll('.prix_materiel');

function calculMateriel() {
    const materielChoisi = document.querySelector('input[name="materiel"]:checked');

    if (materielChoisi.value === 'oui'){
        // affiche le prix de la location pour les deux span qui ont la class prix_materiel
        prixMateriel.forEach((prix) => {
            prix.textContent = '20,00';
        });
    } else {
        prixMateriel.forEach((prix) => {
            prix.textContent = '0,00';
        });
    }
}

// Recalcule le prix à chaque changement de choix
choixMateriel.forEach((choix) => {
    choix.addEventListener('change', () => {
        calculMateriel();
        calculTotal();
    });
});


// PRIX TOTAL
const prixTotal = document.querySelector('#prix_total');

function calculTotal(){
    // Ne calcule pas le total si le prix de livraison est inconnu
    if (prixLivraison.textContent === '--') {
        prixTotal.textContent = '--';
        return;
    }

    const total = 
        Number(sousTotal.textContent) + 
        Number(prixLivraison.textContent) + 
        // récupère le 1e élément car 2 élément ont cette classe
        // remplace la , d'affichage par . pour le calcul
        Number(prixMateriel[0].textContent.replace(',', '.'));

    prixTotal.textContent = total.toFixed(2);
}


// Calculs au chargement de la page
calculSousTotal();
calculMateriel();
calculTotal();




// RECUPERATION DES DONNEES AVANT ENVOI DU FORMULAIRE
const formCommande = document.querySelector('#form_commande');
const formNbreConvives = document.querySelector('#form_nbre_convives');

const dateLivraison = document.querySelector('#date_livraison');
const heureLivraison = document.querySelector('#heure_livraison');

const formDate = document.querySelector('#form_date');
const formHeure = document.querySelector('#form_heure');

const formMateriel = document.querySelector('#form_materiel');

const cgv = document.querySelector('#cgv');

// Vérifie que toutes les conditions sont remplies pour valider la commande
function verifierCommande() {
    // Vérifie que le prix de livraison est disponible
    const livraisonOK =
        prixLivraison.textContent !== '--' &&
        prixLivraison.textContent !== '';

    // Vérifie que la date et l'heure sont renseignées
    const dateOK = dateLivraison.value !== '';
    const heureOK = heureLivraison.value !== '';

    // Vérifie que les CGV sont acceptées
    const cgvOK = cgv.checked;

    // Active le bouton uniquement si toutes les conditions sont remplies
    btnValiderCommande.disabled = !(livraisonOK && dateOK && heureOK && cgvOK);
}

// Vérifie la commande lorsque la date change
dateLivraison.addEventListener('change', verifierCommande);

// Vérifie la commande lorsque l'heure change
heureLivraison.addEventListener('change', verifierCommande);

// Vérifie la commande lorsque les CGV sont cochées ou décochées
cgv.addEventListener('change', verifierCommande);

// Formulaire d'envoi pour création de la commande
formCommande.addEventListener('submit', (event) => {

    // Vérifie les conditions avant d'envoyer la commande
    verifierCommande();

    // Empêche l'envoi si une condition n'est pas remplie
    if (btnValiderCommande.disabled) {
        event.preventDefault();
        return;
    }

    // récupère le nombre de convives
    formNbreConvives.value = inputNbreConvives.value;
    // récupère la date de livraison
    formDate.value = dateLivraison.value;
    // récupère l'heure de livraison
    formHeure.value = heureLivraison.value;
    // récupère le choix de location du matériel
    const materielChoisi = document.querySelector('input[name="materiel"]:checked');
    formMateriel.value = materielChoisi.value;
});

// Calcul du prix de livraison au chargement
calculLivraison();

// Vérifie les conditions de validation de la commande
verifierCommande();
