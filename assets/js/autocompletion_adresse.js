// OBJECTIF : autocomplétion de l'adresse pour éviter les erreurs qui conduisent à une invalidation de l'adresse et non calcul des frais de livraison

// Applique le même fonctionnement aux champs du formulaire reçu.
function activerAutocompletion(champAdresse, champCodePostal, champVille, listeSuggestions) {

    // Exécute ce code uniquement lorsque les éléments sont présents sur la page.
    if (champAdresse && champCodePostal && champVille && listeSuggestions) {
        // Conserve les dernières propositions et le délai avant la recherche.
        let suggestions = [];
        let delaiRecherche;

        // le délai de recherche évite d'appeler Géoplateforme pour l'autocompletion à chaque lettre tapée
        champAdresse.addEventListener('input', () => {
            clearTimeout(delaiRecherche);

            // Cherche si la saisie correspond à une suggestion sélectionnée.
            const suggestionChoisie = suggestions.find(suggestion => {
                return suggestion.properties.label === champAdresse.value;
            });

            if (suggestionChoisie) {
                // Sépare la rue du code postal et de la ville.
                const adresse = suggestionChoisie.properties;

                champAdresse.value = adresse.name;
                champCodePostal.value = adresse.postcode;
                champVille.value = adresse.city;

                // Retire les suggestions après sélection.
                suggestions = [];
                listeSuggestions.replaceChildren();
                return;
            }

            // Retire les anciennes propositions lorsque la saisie change.
            suggestions = [];
            listeSuggestions.replaceChildren();

            const saisie = champAdresse.value.trim();

            // Attend une saisie suffisante avant de demander des suggestions.
            if (saisie.length < 4) {
                return;
            }

            // Attend 300 millisecondes pour éviter une requête à chaque touche.
            delaiRecherche = setTimeout(async () => {
                try {
                    // Construit l'adresse de la requête en encodant la saisie.
                    const url = new URL('https://data.geopf.fr/geocodage/search');

                    url.searchParams.set('q', saisie);
                    url.searchParams.set('index', 'address');
                    url.searchParams.set('limit', '5');
                    url.searchParams.set('autocomplete', '1');

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error('Les suggestions sont indisponibles.');
                    }

                    const resultat = await response.json();

                    // Ignore une réponse si le client a changé sa saisie entre-temps.
                    if (champAdresse.value.trim() !== saisie) {
                        return;
                    }

                    // Conserve uniquement des résultats utilisables comme adresse.
                    suggestions = resultat.features.filter(suggestion => {
                        const adresse = suggestion.properties;

                        return (
                            (adresse.type === 'housenumber' || adresse.type === 'street')
                            && adresse.name
                            && adresse.postcode
                            && adresse.city
                        );
                    });

                    // Affiche les adresses complètes dans les propositions.
                    suggestions.forEach(suggestion => {
                        const option = document.createElement('option');
                        option.value = suggestion.properties.label;
                        listeSuggestions.appendChild(option);
                    });
                } catch (erreur) {
                    // Une indisponibilité des suggestions laisse la saisie manuelle possible.
                    console.error('Erreur des suggestions d’adresse :', erreur);
                }
            }, 300);
        });
    }
}

// Active les suggestions sur le formulaire d'inscription.
activerAutocompletion(
    document.querySelector('#inscription_user_adresse_postale'),
    document.querySelector('#inscription_user_code_postal'),
    document.querySelector('#inscription_user_ville'),
    document.querySelector('#suggestions_adresse')
);

// Active les suggestions dans la modification du compte.
activerAutocompletion(
    document.querySelector('#informations_user_adresse_postale'),
    document.querySelector('#informations_user_code_postal'),
    document.querySelector('#informations_user_ville'),
    document.querySelector('#suggestions_adresse')
);

// Active les suggestions pour une autre adresse de livraison.
// Ce formulaire est utilisé dans le panier et la modification de commande.
activerAutocompletion(
    document.querySelector('#adresse_livraison_adresse_postale'),
    document.querySelector('#adresse_livraison_code_postal'),
    document.querySelector('#adresse_livraison_ville'),
    document.querySelector('#suggestions_adresse')
);