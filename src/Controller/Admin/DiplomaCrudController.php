<?php

namespace App\Controller\Admin;

use App\Entity\Diploma;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DiplomaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Diploma::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Diplôme / carte")
            ->setEntityLabelInPlural("Diplômes")
            ->setDefaultSort(["position" => "ASC", "id" => "ASC"])
            ->setHelp(Crud::PAGE_INDEX, "Cartes de la section « Diplômes » de la page Références, dans l'ordre d'affichage.");
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new("position", "Ordre")->setHelp("Ordre croissant.");
        yield TextField::new("title", "Titre");
        yield TextEditorField::new("content", "Texte")
            ->setRequired(false)
            ->setHelp("Un paragraphe ou une liste à puces.")
            ->hideOnIndex();
        yield TextField::new("location", "Lieu (optionnel)")
            ->setRequired(false)
            ->setHelp("Affiché en bas de la carte avec une icône de localisation.");
        yield BooleanField::new("showIcon", "Icône diplôme")
            ->renderAsSwitch(false)
            ->setHelp("Icône de diplômé devant le titre.")
            ->hideOnIndex();
        yield BooleanField::new("wide", "Carte large")
            ->renderAsSwitch(false)
            ->setHelp("Pour un texte long.")
            ->hideOnIndex();
    }
}
