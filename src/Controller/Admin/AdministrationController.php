<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdministrationController extends AbstractDashboardController
{
    public function index(): Response
    {
        
        // Option 1. You can make your dashboard redirect to some common page of your backend
        return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

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
        // Liens de gestion
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home')->setPermission('ROLE_ADMIN');

        yield MenuItem::submenu('Commandes', 'fa fa-cart-shopping')->setSubItems([
            MenuItem::linkTo(CommandeCrudController::class,'Commandes', 'fas fa-ellipsis-vertical'),               
            MenuItem::linkTo(CommandeHistoriqueCrudController::class,'Historique de Commandes', 'fas fa-ellipsis-vertical')
        ]);

        yield MenuItem::subMenu('Carte & Menus', 'fa fa-utensils')->setSubItems([
            MenuItem::linkTo(MenuCrudController::class,'Menus', 'fas fa-ellipsis-vertical'),
            MenuItem::linkTo(EntreeCrudController::class,'Entrées', 'fas fa-ellipsis-vertical'),
            MenuItem::linkTo(PlatCrudController::class,'Plats', 'fas fa-ellipsis-vertical'),        
            MenuItem::linkTo(DessertCrudController::class,'Desserts', 'fas fa-ellipsis-vertical'),
        ]);
        
        yield MenuItem::subMenu('Paramètres des menus', 'fa fa-gear')->setSubItems([
            MenuItem::linkTo(AllergeneCrudController::class, 'Allergènes', 'fas fa-ellipsis-vertical'),
            MenuItem::linkTo(ConditionCrudController::class,'Conditions', 'fas fa-ellipsis-vertical'),
            MenuItem::linkTo(RegimeCrudController::class,'Régimes', 'fas fa-ellipsis-vertical'),
            MenuItem::linkTo(ThemeCrudController::class,'Thèmes', 'fas fa-ellipsis-vertical'),    
        ]);

        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users')->setPermission('ROLE_ADMIN');
        

        // // Lien bas de page
        // yield MenuItem::linkToRoute('Retour au site', 'fa fa-link', 'app_accueil')->setCssClass('menu-retour-site');
        // yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out-alt');
    }
}