import './styles/pages/admin.css';

// --------------------------------------------- MODIFICATION DE COMMANDE ----------------------------------------------------------

// Récupère le formulaire : il n'existe que sur la page de détail d'une commande.
const formulaireModification = document.querySelector('#form_modification_commande');

if (formulaireModification) {

    // const btnForm = document.querySelector('.btn2_modifier');
    // const containerCta = document.querySelector('.btn_form');
    // const anciennesDonnees = document.querySelectorAll('.anciennes_donnees');
    // const nouvellesDonnees = document.querySelectorAll('.nouvelles_donnees');

    // if(btnForm && containerCta){
    //     btnForm.addEventListener('click', () => {
    //         // affiche les boutons liés au formulaire
    //         containerCta.style.display = 'flex';

    //         // retire le bouton modifier
    //         btnForm.style.display = 'none';

    //         // retire les anciennes données
    //         anciennesDonnees.forEach(element => {
    //             element.style.display = 'none';
    //         })
            
    //         // affiche les input pour obtenir les nouvelles données
    //         nouvellesDonnees.forEach(element => {
    //             element.style .display = 'table-cell';
    //         })
    //     })
    // }

    // ------------------------------------------------ ELEMENTS DE LA PAGE ------------------------------------------------

    const btnModifier = document.querySelector('.btn2_modifier');
    const btnSauvegarder = document.querySelector('#btn_form_sauvegarder');
    const containerCta = document.querySelector('.btn_form');

    const anciennesDonnees = document.querySelectorAll('.anciennes_donnees');
    const nouvellesDonnees = document.querySelectorAll('.nouvelles_donnees');

    // Champs générés par le ModifierCommandeType
    const inputConvives = document.querySelector('#modifier_commande_nbrePers');
    const inputMateriel = document.querySelector('#modifier_commande_pretMateriel');
    const inputAdresse = document.querySelector('#modifier_commande_adresseLivraison');
    const inputCodePostal = document.querySelector('#modifier_commande_codePostalLivraison');
    const inputVille = document.querySelector('#modifier_commande_villeLivraison');

    // Eléments liés à la vérification d'adresse.
    const complementAdresse = document.querySelector('#complement_adresse');
    const messageLivraison = document.querySelector('#message_livraison');
    const btnVerifierAdresse = document.querySelector('#reessayer_livraison');

    // Emplacements des valeurs de facturation.
    const recapPrixPersonne = document.querySelector('#recap_prixPerPers');
    const recapConvives = document.querySelector('#recap_convives');
    const recapSousTotal = document.querySelector('#recap_sous_total');
    const recapReduction = document.querySelector('#recap_reduction');
    const recapLivraison = document.querySelector('#recap_livraison');
    const recapMateriel = document.querySelector('#recap_materiel');
    const recapTotal = document.querySelector('#recap_total');

    // ------------------------------------------------ DONNEES DE CALCUL ------------------------------------------------

    // Transforme les valeurs des data-* en nombres.
    const prixPersonne = Number(formulaireModification.dataset.prixPersonne);
    const minimumConvives = Number(formulaireModification.dataset.minimumConvives);
    const tauxReduction = Number(formulaireModification.dataset.tauxReduction);
    const convivesSupplementaires = Number(formulaireModification.dataset.convivesSupplementaires);
    const forfaitMateriel = Number(formulaireModification.dataset.forfaitMateriel);

    const livraisonInitiale = Number(formulaireModification.dataset.livraisonInitiale);

    const adresseInitiale = formulaireModification.dataset.adresseInitiale.trim();
    const urlLivraison = formulaireModification.dataset.urlLivraison;

    // Ces valeurs pourront changer pendant la modification.
    let livraisonActuelle = livraisonInitiale;
    let livraisonValide = true;

    // ------------------------------------------------ AFFICHAGE ----------------------------------------------------------

    btnModifier.addEventListener('click', () => {
        // Affiche les boutons Sauvegarder et Abandonner.
        containerCta.style.display = 'flex';

        // Masque le bouton Modifier.
        btnModifier.style.display = 'none';

        // Masque les anciennes informations
        anciennesDonnees.forEach(element => {
            element.style.display = 'none';
        });

        // Affiche les cellules contenant les champs du formulaire.
        nouvellesDonnees.forEach(element => {
            element.style.display = 'table-cell';
        });

        verifierChangementAdresse();
    });

    // ------------------------------------------------ FACTURATION ----------------------------------------------------------

    function calculFacturation() {
        const nombreConvives = Number(inputConvives.value);

        recapPrixPersonne.textContent = prixPersonne.toFixed(2);

        // si le matériel est coché, utilise le forfaitMateriel sinon utilise 0
        const montantMateriel = inputMateriel.checked ? forfaitMateriel : 0;
        recapMateriel.textContent = montantMateriel.toFixed(2);

        // livraisonValide = l'adresse n'a pas changée (et donc le prix de livraison)
        // Si l'adresse a changé sans être vérifiée, le prix est inconnu (car non calculé)
        if (livraisonValide) {
            recapLivraison.textContent = livraisonActuelle.toFixed(2);
        } else {
            recapLivraison.textContent = '--';
        }

        // Ne calcule aucun prix avec un nombre de convives invalide : si nbre de convives = '', s'il n'est pas un int, s'il est < au nbre min de convives, s'il est invalide
        if (
            inputConvives.value === ''
            || !Number.isInteger(nombreConvives)
            || nombreConvives < minimumConvives
            || !inputConvives.validity.valid
        ) {
            recapConvives.textContent = '--';
            recapSousTotal.textContent = '--';
            recapReduction.textContent = '--';
            recapTotal.textContent = '--';
            return;
        }

        // Calcule le sous-total = prix AVANT réduction
        // prix par personne × nombre de convives (round : pour arrondir ,00)
        const sousTotal = Math.round(nombreConvives * prixPersonne * 100) / 100;

        // Calcule le seuil donnant droit à la réduction :
        // minimum du menu + 5 convives.
        const seuilReduction = minimumConvives + convivesSupplementaires;

        // Par défaut, aucune réduction.
        let montantReduction = 0;

        // Applique la réduction de 10 % si le seuil est atteint.
        if (nombreConvives >= seuilReduction) {
            montantReduction = Math.round(sousTotal * tauxReduction * 100) / 100;
        }

        // Actualise les valeurs affichées dans la facturation.
        recapConvives.textContent = nombreConvives;
        recapSousTotal.textContent = sousTotal.toFixed(2);
        recapReduction.textContent = montantReduction.toFixed(2);

        // Si les frais de livraison ne sont pas encore vérifiés,
        // n'affiche pas de total qui pourrait être incorrect.
        if (!livraisonValide) {
            recapTotal.textContent = '--';
            return;
        }

        // Calcule le total = sous-total - réduction + livraison + matériel.
        const total = Math.round((sousTotal - montantReduction + livraisonActuelle + montantMateriel) * 100) / 100;

        recapTotal.textContent = total.toFixed(2);
    }

    // Recalcule quand les convives ou le matériel changent.
    inputConvives.addEventListener('input', calculFacturation);
    inputMateriel.addEventListener('change', calculFacturation);


    // ------------------------------------------------ CHANGEMENT D'ADRESSE ------------------------------------------------

    function verifierChangementAdresse() {
        // Compare l'adresse saisie avec l'adresse initiale (elles ne contiennent plus le nom et le prénom)
        const adresseModifiee = inputAdresse.value.trim() !== adresseInitiale;

        if (!adresseModifiee) {
            // L'adresse est identique : conserve les frais de livraison de base
            complementAdresse.hidden = true;
            messageLivraison.hidden = true;
            btnVerifierAdresse.hidden = true;

            // Ces champs sont nécessaires uniquement pour vérifier une nouvelle adresse
            inputCodePostal.required = false;
            inputVille.required = false;

            livraisonActuelle = livraisonInitiale;
            livraisonValide = true;

            btnSauvegarder.disabled = false;
        } else {
            // L'adresse a changé : demande les informations nécessaires au service de calcul
            complementAdresse.hidden = false;
            messageLivraison.hidden = false;
            btnVerifierAdresse.hidden = false;

            inputCodePostal.required = true;
            inputVille.required = true;

            // Attend la vérification de l'adresse et le nouveau prix de livraison.
            livraisonValide = false;
            btnSauvegarder.disabled = true;

            messageLivraison.textContent =
                "Renseignez le code postal et la ville correspondant à cette adresse, puis cliquez sur Vérifier l’adresse.";

            btnVerifierAdresse.textContent = "Vérifier l’adresse";
        }

        // Actualise la facturation selon l'état de la livraison.
        calculFacturation();
    }

    // Si l'adresse, le code postal ou la ville change, relance la vérification avant de sauvegarder
    inputAdresse.addEventListener('input', verifierChangementAdresse);
    inputCodePostal.addEventListener('input', verifierChangementAdresse);
    inputVille.addEventListener('input', verifierChangementAdresse);


    // ------------------------------------------------ CALCUL DE LIVRAISON ------------------------------------------------

    btnVerifierAdresse.addEventListener('click', async () => {
        // Vérifie les champs d'adresse avant d'appeler le serveur.
        if (
            !inputAdresse.reportValidity()
            || !inputCodePostal.reportValidity()
            || !inputVille.reportValidity()
        ) {
            return;
        }

        // Refuse une ville composée uniquement d'espaces ou vide
        if (inputVille.value.trim() === '') {
            messageLivraison.textContent = 'Veuillez renseigner la ville.';
            return;
        }

        // Conserve les valeurs renseignée
        const adresseEnvoyee = inputAdresse.value.trim();
        const codePostalEnvoye = inputCodePostal.value.trim();
        const villeEnvoyee = inputVille.value.trim();

        // Désactive le bouton pour éviter plusieurs calculs simultanés
        btnVerifierAdresse.disabled = true;
        btnSauvegarder.disabled = true;

        livraisonValide = false;
        messageLivraison.hidden = false;
        messageLivraison.textContent = 'Calcul de la livraison en cours…';
        calculFacturation();

        try {
            // Prépare les données attendues par le contrôleur de livraison.
            const donnees = new FormData();
            donnees.append('adresse', adresseEnvoyee);
            donnees.append('codePostal', codePostalEnvoye);
            donnees.append('ville', villeEnvoyee);

            const response = await fetch(urlLivraison, {
                method: 'POST',
                body: donnees,
            });

            // Une erreur technique peut renvoyer autre chose que du JSON.
            if (!response.ok) {
                throw new Error(
                    'Impossible de vérifier cette adresse. Vérifiez votre saisie ou réessayez.'
                );
            }

            const resultat = await response.json();

            // Vérifie que le serveur a bien renvoyé un prix.
            if (
                typeof resultat.prixLivraison !== 'number'
                || !Number.isFinite(resultat.prixLivraison)
                || resultat.prixLivraison < 0
            ) {
                throw new Error('Le prix de livraison reçu est invalide.');
            }

            // Si la saisie a changé pendant la demande, ne garde pas ce prix.
            if (
                inputAdresse.value.trim() !== adresseEnvoyee
                || inputCodePostal.value.trim() !== codePostalEnvoye
                || inputVille.value.trim() !== villeEnvoyee
            ) {
                verifierChangementAdresse();
                return;
            }

            livraisonActuelle = resultat.prixLivraison;
            livraisonValide = true;
            btnSauvegarder.disabled = false;

            messageLivraison.textContent =
                'Adresse vérifiée. Les frais de livraison ont été actualisés.';

            calculFacturation();
        } catch (erreur) {
            console.error('Erreur lors du calcul de livraison :', erreur);

            // Si l'adresse a changé entre-temps, conserve l'état de la saisie actuelle.
            if (
                inputAdresse.value.trim() !== adresseEnvoyee
                || inputCodePostal.value.trim() !== codePostalEnvoye
                || inputVille.value.trim() !== villeEnvoyee
            ) {
                verifierChangementAdresse();
                return;
            }

            livraisonValide = false;
            btnSauvegarder.disabled = true;

            messageLivraison.textContent = erreur.message;
            btnVerifierAdresse.textContent = 'Réessayer';

            calculFacturation();
        } finally {
            // Le calcul est terminé, qu'il ait réussi ou échoué.
            btnVerifierAdresse.disabled = false;
        }
    });

    // ------------------------------------------------ ENVOI DU FORMULAIRE ------------------------------------------------

    formulaireModification.addEventListener('submit', event => {
        // Empêche l'envoi avec des frais de livraison non vérifiés.
        if (!livraisonValide) {
            event.preventDefault();

            messageLivraison.hidden = false;
            messageLivraison.textContent =
                'Vérifiez l’adresse avant de sauvegarder.';
        }

        // Les champs Symfony sont envoyés automatiquement.
        // Il n'est pas nécessaire de recopier leur valeur dans des champs cachés.
        // La validation HTML native contrôle les champs obligatoires et leur format.
    });
}




// --------------------------------------------- ANNULATION DE COMMANDE -----------------------------------------------------------------
// Permet d'ouvrir automatiquement la modale d'annulation en cas de formulaire invalide
document.addEventListener('DOMContentLoaded', () => {
    const modale = document.getElementById('annulationCommandeModal');

    if (modale && modale.dataset.ouvrir === 'true') {
        bootstrap.Modal.getOrCreateInstance(modale).show();
    }
});

