from pathlib import Path
from xml.sax.saxutils import escape
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from reportlab.lib.pagesizes import A4

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / 'output/pdf/ECF_plan_tests_2026-11-10.pdf'
OUT.parent.mkdir(parents=True, exist_ok=True)
styles = getSampleStyleSheet()
styles.add(ParagraphStyle(name='TitreECF', fontName='Helvetica-Bold', fontSize=23, leading=27, textColor=colors.HexColor('#603b24'), spaceAfter=12))
styles.add(ParagraphStyle(name='IntroECF', fontSize=10, leading=14, spaceAfter=10))
styles.add(ParagraphStyle(name='CellECF', fontSize=9, leading=12))
styles.add(ParagraphStyle(name='PetitECF', fontSize=8, leading=11, textColor=colors.HexColor('#555555')))

pages = [
('1. Comptes et accès client', 'Commencer avec un compte de test et garder José dans un autre navigateur ou une fenêtre privée.', [
('C01', 'Créer un nouveau client avec une boîte Yopmail accessible.', 'Compte créé, connexion possible, mail de bienvenue reçu. Utiliser uniquement des données fictives.'),
('C02', 'Soumettre une inscription vide, un email invalide puis un email déjà utilisé.', 'Erreurs compréhensibles, aucune création supplémentaire ; vérifier aussi les règles de mot de passe.'),
('C03', 'Se connecter avec un bon puis un mauvais mot de passe ; se déconnecter.', 'Bon compte ouvert ; échec lisible sans erreur technique ; pages privées protégées après déconnexion.'),
('C04', 'Modifier les coordonnées puis recharger la page.', 'Valeurs conservées ; les coordonnées obligatoires du client ne peuvent pas être effacées.'),
('C05', 'Changer son mot de passe depuis le compte.', 'Ancien mot de passe refusé à la prochaine connexion, nouveau accepté ; erreur si confirmation différente.'),
('C06', 'Demander un lien de réinitialisation avec un email connu puis inconnu.', 'Même réponse générique ; mail reçu pour le compte existant, lien absolu non vide.'),
('C07', 'Utiliser le lien reçu et choisir un nouveau mot de passe.', 'Connexion possible avec le nouveau mot de passe ; réutilisation du même lien refusée proprement.'),
('C08', 'Essayer un lien modifié ou expiré après 10 minutes.', 'Refus sans erreur serveur ; proposition de demander un nouveau lien.'),
('C09', 'Ouvrir la liste des commandes avec un compte sans commande.', 'Liste vide compréhensible, aucune 404 sur la liste. La 404 reste prévue pour un détail introuvable.'),
]),
('2. Catalogue, panier et création', 'Choisir quelques cas ciblés : pas besoin de tester les 24 commandes. Noter les montants et le stock avant chaque opération.', [
('O01', 'Parcourir catalogue, filtres, galerie et détail de menu.', 'Nom, composition, prix, minimum, conditions, allergènes et disponibilité cohérents ; liens utilisables.'),
('O02', 'Ajouter/retirer un menu du panier ; consulter panier vide et rempli.', 'Bon menu et bonnes informations ; état vide propre ; pas de bloc superposé ou de lien cassé.'),
('O03', 'Commander au minimum de convives, puis en dessous.', 'Minimum accepté ; quantité insuffisante refusée sans création ni retrait de stock.'),
('O04', 'Comparer minimum + 4 et minimum + 5 convives.', 'Réduction de 10 % à partir de minimum + 5 ; sous-total après réduction ; arrondis cohérents.'),
('O05', 'Essayer un délai trop court et une date hors disponibilité.', 'Refus explicite ; délai du menu respecté (7 ou 30 jours selon les conditions).'),
('O06', 'Comparer une adresse Bordeaux et une adresse hors Bordeaux ; essayer une adresse invalide.', 'Bordeaux gratuit ; hors Bordeaux selon le calcul existant ; adresse invalide sans prix réutilisé à tort.'),
('O07', 'Comparer avec et sans matériel.', 'Forfait matériel de 20 euros si prêt ; total = sous-total + livraison + matériel.'),
('O08', 'Valider une commande réelle de test et relever son numéro.', 'Une commande, premier historique, stock diminué de 1, panier vidé, confirmation et mail corrects.'),
('O09', 'Vérifier un menu désactivé ou sans stock.', 'Pas de nouvelle commande possible, même s’il était déjà dans le panier ; anciennes commandes conservées.'),
]),
('3. Modifier et annuler', 'Utiliser les commandes de démonstration en attente ou acceptées. Prévoir une autre commande pour chaque annulation, action définitive.', [
('M01', 'Client : modifier une commande au statut 1.', 'Saisie, récapitulatif et montants enregistrés identiques ; historique/intervention selon le parcours prévu.'),
('M02', 'Client : changer l’adresse et utiliser Réessayer après une erreur de vérification.', 'La nouvelle adresse est utilisée ; ancien tarif de livraison non conservé sans validation.'),
('M03', 'Client : tenter modification/annulation hors statut autorisé.', 'Client autorisé uniquement au statut 1 ; refus côté serveur et pas seulement bouton masqué.'),
('M04', 'Employé : modifier aux statuts 1 et 2, puis refaire une modification.', 'Modifications successives possibles ; recalcul correct, auteur et intervention conservés.'),
('M05', 'Employé : provoquer une erreur de saisie en modification.', 'Erreur visible ; champs refermés selon ton choix ; nouveau clic Modifier pour reprendre.'),
('M06', 'Annuler une commande côté client.', 'Statut 8, historique et informations d’annulation corrects ; stock augmenté une seule fois.'),
('M07', 'Employé : annuler avec contact et motif renseignés.', 'Autorisé aux statuts 1/2/3 ; auteur, date, moyen de contact et motif conservés et affichés.'),
('M08', 'Employé : omettre le motif, le contact ou la confirmation de contact.', 'Formulaire refusé ; aucune annulation ni modification du stock.'),
('M09', 'Revenir sur une commande annulée et retenter l’action.', 'Actions indisponibles/refusées ; aucun deuxième crédit de stock ; ancien détail consultable.'),
]),
('4. Suivi, mails et avis', 'Un parcours complet est nécessaire : statut 7 > mail reçu > lien > dépôt > modération. Les avis des fixtures ne prouvent pas ce parcours.', [
('A01', 'Changer le statut d’une commande depuis son détail EasyAdmin.', 'Statut enregistré, historique daté et visible côté client ; simple consultation sans écriture.'),
('A02', 'Soumettre le même statut si l’interface le permet.', 'Pas de ligne d’historique ni de mail supplémentaire inutile.'),
('A03', 'Passer une commande avec matériel au statut 6.', 'Mail reçu : restitution, délai de 10 jours ouvrés, information des 600 euros et contact corrects.'),
('A04', 'Passer au statut 7 avec matériel, puis sans matériel sur une autre commande.', 'Restitution enregistrée si prêt ; mail d’invitation à déposer un avis et lien vers la bonne commande.'),
('A05', 'Déposer un avis sur une commande terminée sans avis.', 'Note et texte conservés, date et commande correctes, statut initial en attente, confirmation lisible.'),
('A06', 'Essayer un formulaire vide, une deuxième publication et une commande non terminée.', 'Refus cohérents : note 1 à 5, commentaire selon contraintes, un seul avis, commande au statut 7.'),
('A07', 'Dans EasyAdmin, valider un avis puis en refuser un autre.', 'Seul le statut est modifiable ; pas de création employé ; seuls les validés paraissent sur l’accueil.'),
('A08', 'Contrôler prénom, note, texte et ordre sur l’accueil.', 'Prénom du client de la commande ; avis du plus récent au plus ancien ; aucune donnée privée affichée.'),
('A09', 'Contrôler les variables des mails déjà reçus et les liens.', 'Prénom/numéro/lien corrects, aucun champ vide ; bienvenue et confirmation ont déjà fonctionné auparavant.'),
]),
('5. Employés, administration et statistiques', 'Créer et désactiver les employés manuellement comme prévu. Les commandes fictives alimentent les écrans ; comparer seulement quelques calculs.', [
('J01', 'José : créer un employé avec le mot de passe choisi.', 'Rôle employé imposé, coordonnées facultatives, mot de passe hashé en base ; aucun mot de passe dans le mail.'),
('J02', 'Recevoir le mail et se connecter comme employé.', 'Message demandant de contacter José ; accès aux fonctions employé, pas aux fonctions réservées admin.'),
('J03', 'Désactiver le compte puis essayer une nouvelle connexion.', 'Connexion refusée avec message ; tester séparément la réactivation. Session déjà ouverte : évolution différée.'),
('J04', 'Consulter listes, détails, filtres et formulaires des CRUD.', 'Filtres commandes client/statut corrects ; champs prévus non modifiables ; pas d’action interdite.'),
('J05', 'Contrôler le tableau de bord sans filtre puis avec date/menu.', 'Quantités réelles ; commandes non annulées ; CA seulement statuts 5/6/7 ; période par date de livraison.'),
('J06', 'Combiner filtres, réinitialiser, essayer une période sans résultat et des dates inversées.', 'Tous les blocs suivent les filtres ; état vide propre ; période inversée signalée.'),
('J07', 'Comparer quelques commandes SQL et les montants affichés.', 'CA menus = sous-totaux après réduction ; CA global inclut livraison et matériel ; mois et quantités corrects.'),
('J08', 'Observer MongoDB après création, modification, statut et annulation.', 'Document du bon ID actualisé ; montants/dates/statut concordent avec SQL ; annulation exclue du comptage.'),
('J09', 'Relancer la synchronisation manuelle si nécessaire.', 'Documents mis à jour sans doublons ; conserver le bilan. La synchronisation a déjà fonctionné auparavant.'),
]),
('6. Carrousel, responsive et accessibilité', 'Le responsive a déjà fait l’objet de contrôles de géométrie et de vues. Ici : vérification finale des nouveaux avis et des états non encore couverts.', [
('V01', 'Ordinateur : consulter le carrousel et cliquer sur les deux flèches.', 'Trois cartes visibles à partir de 1200 px ; déplacement d’une carte ; boutons désactivés aux extrémités.'),
('V02', 'Mobile/tablette : glisser les cartes au doigt.', 'Une carte visible sous 1200 px ; glissement horizontal fluide, arrêt sur carte ; pas de débordement de page.'),
('V03', 'Redimensionner pendant que le carrousel est décalé.', 'Cartes et boutons restent cohérents ; toutes les cartes restent accessibles ; aucun défilement automatique.'),
('V04', 'Contrôler avec zéro, un ou trois avis validés si tu disposes de ces états.', 'Section conservée ; message si vide ; pas de flèches inutiles ; ne pas supprimer les données pour ce seul test.'),
('V05', 'Naviguer avec Tab, Entrée et les flèches du clavier.', 'Focus visible ; carrousel, menu burger, formulaires et modales utilisables sans souris ; aucun blocage clavier.'),
('V06', 'Parcourir à nouveau mobile, tablette et grand écran.', 'Pas de chevauchement ; header/menu, catalogue, compte, panier, commandes et pied de page lisibles.'),
('V07', 'Ouvrir EasyAdmin en clair/sombre, notamment sur mobile.', 'Sidebar, retour au site, boutons, tableaux défilants, formulaires et statistiques lisibles.'),
('V08', 'Contrôler panier vide, confirmation, reset valide et erreurs longues.', 'Messages visibles, pas de texte coupé ; modales et boutons accessibles ; fermeture cohérente.'),
('V09', 'Comparer au moins deux navigateurs et un téléphone réel si disponible ; zoomer à 200 %.', 'Contenu accessible, labels lisibles, contrastes suffisants, pas d’information portée uniquement par la couleur.'),
]),
('7. Contact, droits et contrôles techniques', 'Les contrôles de concurrence, CSRF et pannes simulées se préparent avec aide. Ne pas modifier au hasard les clés, données ou réglages pour les provoquer.', [
('S01', 'Envoyer un contact avec une boîte de test puis essayer les champs invalides.', 'Message reçu par l’entreprise avec les bonnes variables ; confirmation ; erreurs lisibles sans perte inutile de saisie.'),
('S02', 'Sans connexion, ouvrir une URL de compte et une URL admin.', 'Authentification exigée ; aucune information privée accessible.'),
('S03', 'Client A : ouvrir l’URL d’une commande ou d’un avis du client B.', 'Accès refusé/404 ; aucune consultation, modification, annulation ou publication pour autrui.'),
('S04', 'Client et employé : essayer une URL réservée à José.', 'Refus côté serveur, même si le lien est absent de la navigation ; tableau de bord et employés protégés.'),
('S05', 'Ouvrir un détail de commande avec un ID inexistant.', '404 propre, sans erreur Twig ; ne pas confondre avec une liste de commandes vide.'),
('S06', 'Avec accompagnement : envoyer une action sensible sans jeton CSRF valide.', 'Action refusée ; base et stock inchangés.'),
('S07', 'Avec accompagnement : deux actions simultanées sur une même commande.', 'Pas de double annulation, de stock crédité deux fois ni d’enregistrement fondé sur un statut périmé.'),
('S08', 'Avec accompagnement : simuler un échec de mail ou de MongoDB.', 'Commande/compte SQL conservé ; retour adapté ; contact/reset traités selon leur parcours ; aucun secret exposé.'),
('S09', 'Conserver une trace de l’audit des dépendances et des incidents.', 'Dernier audit sans alerte connu ; noter les erreurs sans copier mot de passe, token, clé API ou URI privée.'),
]),
('8. Contrôles après déploiement et bilan', 'Cette page concerne la future mise en ligne. Elle ne signifie pas que l’hébergement, les documents ou les fonctionnalités ont déjà été validés.', [
('L01', 'Ouvrir le site déployé en HTTPS, pages publiques et assets.', 'Images, favicon, CSS/JS présents ; aucun lien localhost ; pas d’erreur technique ou mode debug visible.'),
('L02', 'Vérifier la connexion SQL et MongoDB sur le site en ligne.', 'Catalogue, commandes et statistiques accessibles ; secrets configurés hors dépôt public.'),
('L03', 'Refaire un parcours court en ligne : client > commande > statut > avis.', 'Mails et liens pointent vers la bonne URL publique ; SQL, MongoDB et affichage restent cohérents.'),
('L04', 'Vérifier contact, reset et connexion employé en ligne.', 'Boîtes accessibles, liens utilisables, droits conservés après déploiement.'),
('L05', 'Relire les liens du footer et les pages légales.', 'URL, hébergeur et contacts réels renseignés ; descriptions cohérentes avec les traitements déployés.'),
('L06', 'Vérifier les consignes d’installation sur un environnement séparé si possible.', 'README exploitable, schéma/données et configuration documentés, comptes de démonstration disponibles.'),
('L07', 'Reporter les résultats et les corrections dans le suivi de projet.', 'Séparer réussi, échec et non testé ; noter la correction puis retester seulement le scénario concerné.'),
]),
]

def p(text, style='CellECF'):
    return Paragraph(escape(text), styles[style])

story=[]
for idx,(title,intro,tests) in enumerate(pages):
    if idx: story.append(PageBreak())
    story.append(p('VITE & GOURMAND | PLAN DE TESTS', 'PetitECF'))
    story.append(Spacer(1,8))
    story.append(p(title, 'TitreECF'))
    story.append(p(intro, 'IntroECF'))
    if idx == 0:
        story.append(p('Préparé le 10 novembre 2026. Relecture du carrousel effectuée, sans exécution de tests. Cocher après vérification : OK / KO / NT (non testé). Sauvegarder la base avant les essais destructifs.', 'PetitECF'))
        story.append(Spacer(1,9))
    story.append(p('Date : __________________  Navigateur / appareil : __________________', 'PetitECF'))
    story.append(Spacer(1,10))
    rows=[[p('Repère'),p('Action à effectuer'),p('Résultat attendu')]]
    for code,action,expected in tests:
        rows.append([p(code+'\nOK / KO / NT'),p(action),p(expected)])
    table=Table(rows,colWidths=[65,198,248],hAlign='LEFT')
    table.setStyle(TableStyle([
        ('BACKGROUND',(0,0),(-1,0),colors.HexColor('#f7e0c4')),
        ('VALIGN',(0,0),(-1,-1),'TOP'),
        ('LEFTPADDING',(0,0),(-1,-1),8),('RIGHTPADDING',(0,0),(-1,-1),8),
        ('TOPPADDING',(0,0),(-1,-1),8),('BOTTOMPADDING',(0,0),(-1,-1),8),
        ('LINEBELOW',(0,0),(-1,0),0.8,colors.HexColor('#daa481')),
        ('LINEBELOW',(0,1),(-1,-1),0.4,colors.HexColor('#dddddd')),
        ('ROWBACKGROUNDS',(0,1),(-1,-1),[colors.white,colors.HexColor('#fcfaf7')]),
    ]))
    story.append(table)
    story.append(Spacer(1,13))
    story.append(p('Notes / repères des commandes / problèmes à reprendre :', 'PetitECF'))
    story.append(p('____________________________________________________________________________________', 'PetitECF'))
    story.append(p('____________________________________________________________________________________', 'PetitECF'))
    if idx==7:
        story.append(Spacer(1,12))
        story.append(p('Hors de cette campagne obligatoire : reprise automatique MongoDB, révocation des sessions déjà ouvertes après désactivation et renforcements du reset restent des évolutions différées. Les notes 4/5 des fixtures n’empêchent pas de vérifier les limites 1 à 5 du formulaire.', 'PetitECF'))

def footer(canvas, doc):
    canvas.saveState()
    canvas.setStrokeColor(colors.HexColor('#daa481'))
    canvas.line(42,37,A4[0]-42,37)
    canvas.setFont('Helvetica',8)
    canvas.setFillColor(colors.HexColor('#603b24'))
    canvas.drawString(42,24,'ECF - Campagne manuelle | Aucun résultat présumé')
    canvas.drawRightString(A4[0]-42,24,str(doc.page))
    canvas.restoreState()

doc=SimpleDocTemplate(str(OUT),pagesize=A4,rightMargin=42,leftMargin=42,topMargin=33,bottomMargin=50,title='ECF Vite & Gourmand - Plan de tests',author='Claire - ECF')
doc.build(story,onFirstPage=footer,onLaterPages=footer)
print(OUT)
