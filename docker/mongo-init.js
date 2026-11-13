// Récupère le nom de la base depuis les variables du conteneur.
const nomBase = process.env.MONGODB_DATABASE;

// Sélectionne la base utilisée par l'application.
const baseApplication = db.getSiblingDB(nomBase);

// Crée un utilisateur autorisé à lire et modifier cette base uniquement.
baseApplication.createUser({
    user: process.env.MONGO_APP_USERNAME,
    pwd: process.env.MONGO_APP_PASSWORD,
    roles: [
        {
            role: "readWrite",
            db: nomBase,
        },
    ],
});