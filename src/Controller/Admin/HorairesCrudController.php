<?php

namespace App\Controller\Admin;

use App\Entity\Horaires;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class HorairesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Horaires::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('jour')->setLabel('Jour')->setChoices([
                'Lundi' => 1,
                'Mardi' => 2,
                'Mercredi' => 3,
                'Jeudi' => 4,
                'Vendredi' => 5,
                'Samedi' => 6,
                'Dimanche' => 7,
            ]),
            TimeField::new('matinDebut')->setLabel('Matin'),
            TimeField::new('matinFin')->setLabel('Matin'),
            TimeField::new('apresMidiDebut')->setLabel('Après-midi'),
            TimeField::new('apresMidiFin')->setLabel('Après-midi'),
        ];
    }
    
}
