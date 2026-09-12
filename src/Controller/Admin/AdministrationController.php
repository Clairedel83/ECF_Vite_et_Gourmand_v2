<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
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

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-list');
        yield MenuItem::linkTo(AllergeneCrudController::class, 'Allergènes', 'fas fa-list');
        yield MenuItem::linkTo(ConditionCrudController::class,'Conditions', 'fas fa-list');
        yield MenuItem::linkTo(EntreeCrudController::class,'Entrées', 'fas fa-list');
        yield MenuItem::linkTo(DessertCrudController::class,'Desserts', 'fas fa-list');
        yield MenuItem::linkTo(MenuCrudController::class,'Menus', 'fas fa-list');
        yield MenuItem::linkTo(PlatCrudController::class,'Plats', 'fas fa-list');
        yield MenuItem::linkTo(RegimeCrudController::class,'Régimes', 'fas fa-list');
        yield MenuItem::linkTo(ThemeCrudController::class,'Thèmes', 'fas fa-list');
    }
}
