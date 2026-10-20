<?php

namespace App\Controller\Admin;

use App\Entity\Fee;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FeeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fee::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Honoraire")
            ->setEntityLabelInPlural("Honoraires")
            ->setDefaultSort(["position" => "ASC", "id" => "ASC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("id")->hideOnForm(),
            IntegerField::new("position", "Ordre d'apparition")
                ->setHelp("Les honoraires sont affichés par ordre croissant."),
            TextField::new("title", "Titre")
                ->setHelp("Ex : Consultation individuelle (les « : » sont ajoutés automatiquement)."),
            TextareaField::new("text", "Texte")
                ->setNumOfRows(4)
                ->setHelp("Un retour à la ligne est conservé à l'affichage."),
        ];
    }
}
