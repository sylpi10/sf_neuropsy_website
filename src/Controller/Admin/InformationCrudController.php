<?php

namespace App\Controller\Admin;

use App\Entity\Information;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InformationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Information::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("id")->hideOnForm(),
            TextField::new("title", "Titre / Identité"),
            TextField::new("addressLine1", "Adresse ligne 1"),
            TextField::new("addressLine2", "Adresse ligne 2"),
            TextField::new("postalCodeCity", "Code postal et ville"),
            TextField::new("phone", "Telephone"),
            TextField::new("email", "Email"),
            TextField::new("consultationNote", "Précision jours de consultation")
                ->setRequired(false)
                ->setHelp("Affichée sous la liste des jours. Ex : Ouvert pendant les vacances scolaires"),
            BooleanField::new("showQualiopi", "Afficher les logos Qualiopi")->renderAsSwitch(false),
        ];
    }
}
