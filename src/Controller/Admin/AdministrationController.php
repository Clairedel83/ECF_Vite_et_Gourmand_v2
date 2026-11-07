<?php

namespace App\Controller\Admin;

use App\Controller\Admin\HorairesCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use App\Services\StatistiquesCommandeService;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\HttpFoundation\RequestStack;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdministrationController extends AbstractDashboardController
{
    // Rend le service de statistiques disponible dans le contrôleur.
    public function __construct(private StatistiquesCommandeService $statistiquesCommandeService, private RequestStack $requestStack) 
    {
    }

        public function index(): Response
    {
        // Les employés accèdent directement aux commandes.
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_commande_index');
        }

        // Récupère les filtres transmis dans l'URL par le formulaire GET.
        $request = $this->requestStack->getCurrentRequest();

        $dateDebut = $request->query->getString('dateDebut');
        $dateFin = $request->query->getString('dateFin');
        $menuId = $request->query->getInt('menuId');

        $filtres = [];
        $dates = [];
        $fuseau = new \DateTimeZone('Europe/Paris');

        // Vérifie les deux dates lorsqu'elles ont été renseignées.
        foreach (['dateDebut' => $dateDebut, 'dateFin' => $dateFin] as $nom => $valeur) {
            if ($valeur !== '') {
                $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $valeur, $fuseau);

                if (!$date || $date->format('Y-m-d') !== $valeur) {
                    $this->addFlash('warning', 'Veuillez sélectionner une date valide.');

                    return $this->redirectToRoute('admin');
                }

                $dates[$nom] = $date;
            }
        }

        // Empêche une période dont la fin précède le début.
        if (isset($dates['dateDebut'], $dates['dateFin'])
            && $dates['dateDebut'] > $dates['dateFin']) {
            $this->addFlash('warning', 'La date de fin doit être postérieure ou égale à la date de début.');

            return $this->redirectToRoute('admin');
        }

        // Inclut toutes les livraisons à partir du début de la première journée.
        if (isset($dates['dateDebut'])) {
            $filtres['dateLivraison']['$gte'] = new UTCDateTime($dates['dateDebut']);
        }

        // La borne exclusive du lendemain inclut toute la dernière journée.
        if (isset($dates['dateFin'])) {
            $filtres['dateLivraison']['$lt'] = new UTCDateTime(
                $dates['dateFin']->modify('+1 day')
            );
        }

        if ($menuId > 0) {
            $filtres['menuId'] = $menuId;
        }

        // Applique les mêmes filtres aux graphiques et aux indicateurs.
        $commandesParMenu = $this->statistiquesCommandeService->compterCommandesParMenu($filtres);
        $caParMois = $this->statistiquesCommandeService->calculerCaParMois($filtres);
        $caTotal = $this->statistiquesCommandeService->calculerCaTotal($filtres);
        $menus = $this->statistiquesCommandeService->recupererMenus();

        // Additionne les commandes comptées pour chaque menu.
        $nombreCommandes = 0;

        foreach ($commandesParMenu as $resultat) {
            $nombreCommandes += $resultat['nombreCommandes'];
        }

        return $this->render('admin/tableau_de_bord.html.twig', [
            'commandesParMenu' => $commandesParMenu,
            'caParMois' => $caParMois,
            'caTotal' => $caTotal,
            'nombreCommandes' => $nombreCommandes,
            'menus' => $menus,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'menuId' => $menuId,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Vite Et Gourmand');
    }

    // Ajoute un fichier CSS personnalisé à EasyAdmin
    public function configureAssets(): Assets
    {
        return Assets::new()
             ->addAssetMapperEntry('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('GESTION');
        yield MenuItem::submenu('Commandes', 'fa fa-cart-shopping')->setSubItems([
            MenuItem::linkTo(CommandeCrudController::class,'Commandes', 'fas fa-chevron-right'),               
            MenuItem::linkTo(CommandeHistoriqueCrudController::class,'Historiques', 'fas fa-chevron-right')
        ]);

        yield MenuItem::section('MENUS');
        yield MenuItem::subMenu('Carte & Menus', 'fa fa-utensils')->setSubItems([
            MenuItem::linkTo(MenuCrudController::class,'Menus', 'fas fa-chevron-right'),
            MenuItem::linkTo(EntreeCrudController::class,'Entrées', 'fas fa-chevron-right'),
            MenuItem::linkTo(PlatCrudController::class,'Plats', 'fas fa-chevron-right'),        
            MenuItem::linkTo(DessertCrudController::class,'Desserts', 'fas fa-chevron-right'),
        ]);
        
        yield MenuItem::subMenu('Paramètres des menus', 'fa fa-gear')->setSubItems([
            MenuItem::linkTo(AllergeneCrudController::class, 'Allergènes', 'fas fa-chevron-right'),
            MenuItem::linkTo(ConditionCrudController::class,'Conditions', 'fas fa-chevron-right'),
            MenuItem::linkTo(RegimeCrudController::class,'Régimes', 'fas fa-chevron-right'),
            MenuItem::linkTo(ThemeCrudController::class,'Thèmes', 'fas fa-chevron-right'),    
        ]);

        yield MenuItem::section('ADMINISTRATION');
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkTo(HorairesCrudController::class, 'Horaires', 'fas fa-clock');
        yield MenuItem::linkTo(AvisCrudController::class, 'Avis', 'fas fa-comment-dots');
    }
}