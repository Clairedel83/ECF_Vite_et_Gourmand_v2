import Chart from 'chart.js/auto';

// --------------------------------------------- STATISTIQUES DES COMMANDES ----------------------------------------------------------

const statistiquesCommandes = document.querySelector('#statistiques_commandes');

// existe uniquement sur le tableau de bord de l'admin
if (statistiquesCommandes) {

    // Transforme les données transmises par Twig en tableaux JavaScript.
    const commandes = JSON.parse(statistiquesCommandes.dataset.commandes);
    const caParMois = JSON.parse(statistiquesCommandes.dataset.ca);

    const graphiqueCa = document.querySelector('#graphique_ca');
    const graphiqueRepartition = document.querySelector('#graphique_repartition');

    // Palette Chart.js : le rose est utilisé en dernier.
    const palette = [
        '#36A2EB',
        '#FF9F40',
        '#FFCD56',
        '#4BC0C0',
        '#9966FF',
        '#A76574',
        '#FF6384',
    ];

    // Prépare le format d'affichage des montants en euros
    const formatEuro = new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    });

    // ----------------------------------- CA PAR MOIS ---------------------------------------

    const mois = [];
    const montants = [];
    const couleursMois = [];

    caParMois.forEach(function (resultat, index) {

        // Transforme "2026-11" en "11/2026" pour l'affichage.
        const date = resultat.mois.split('-');
        mois.push(date[1] + '/' + date[0]);

        // Transforme le montant en nombre uniquement pour le graphique
        montants.push(Number(resultat.chiffreAffaires));

        // Recommence la palette lorsqu'elle a été entièrement utilisée
        couleursMois.push(palette[index % palette.length]);
    });

    // Affiche une barre verticale pour chaque mois.
    new Chart(graphiqueCa, {
        type: 'bar',
        data: {
            labels: mois,
            datasets: [
                {
                    label: "Chiffre d'affaires",
                    data: montants,
                    backgroundColor: couleursMois,
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        // Affiche le montant en euros au survol d'une barre.
                        label: function (contexte) {
                            return formatEuro.format(contexte.parsed.y);
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Affiche les graduations en euros.
                        callback: function (valeur) {
                            return formatEuro.format(valeur);
                        },
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });

    // ------------------------------------- COMMANDES PAR MENU -------------------------------------

    const nomsMenus = [];
    const nombresCommandes = [];
    const couleursMenus = [];

    commandes.forEach(function (commande, index) {

        // Ajoute le nombre directement dans la légende du camembert.
        nomsMenus.push(commande.menuNom + ' : ' + commande.nombreCommandes);
        nombresCommandes.push(commande.nombreCommandes);
        couleursMenus.push(palette[index % palette.length]);
    });

    new Chart(graphiqueRepartition, {
        type: 'pie',
        data: {
            labels: nomsMenus,
            datasets: [
                {
                    label: 'Nombre de commandes',
                    data: nombresCommandes,
                    backgroundColor: couleursMenus,
                    borderWidth: 0,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 16,
                        boxHeight: 16,
                        padding: 18,
                    },
                },
                tooltip: {
                    callbacks: {
                        // Le libellé contient déjà le menu et son nombre de commandes.
                        label: function (contexte) {
                            return contexte.label + ' commande(s)';
                        },
                    },
                },
            },
        },
    });
}