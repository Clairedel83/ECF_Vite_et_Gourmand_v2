import './styles/pages/admin.css';

// Permet d'ouvrir automatiquement la modale d'annulation en cas de formulaire invalide
document.addEventListener('DOMContentLoaded', () => {
    const modale = document.getElementById('annulationCommandeModal');

    if (modale?.dataset.ouvrir === 'true') {
        bootstrap.Modal.getOrCreateInstance(modale).show();
    }
});