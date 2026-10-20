<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ExperienceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Experience::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Expérience")
            ->setEntityLabelInPlural("Expériences")
            ->setDefaultSort(["current" => "DESC", "position" => "ASC", "id" => "ASC"])
            ->setPaginatorPageSize(50)
            ->setHelp(Crud::PAGE_INDEX, "Lignes de la section « Expériences » de la page Références, regroupées en « Actuelle » et « Antérieures ».");
    }

    public function configureFields(string $pageName): iterable
    {
        yield BooleanField::new("current", "Actuelle")
            ->renderAsSwitch(false)
            ->setHelp("Cochée : sous « Actuelle ». Décochée : sous « Antérieures ».");
        yield IntegerField::new("position", "Ordre")->setHelp("Ordre croissant, dans son groupe.");
        yield TextareaField::new("label", "Expérience")->setNumOfRows(2);
    }
}
