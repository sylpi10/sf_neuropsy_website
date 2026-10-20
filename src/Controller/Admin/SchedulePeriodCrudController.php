<?php

namespace App\Controller\Admin;

use App\Entity\SchedulePeriod;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SchedulePeriodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SchedulePeriod::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Période exceptionnelle")
            ->setEntityLabelInPlural("Périodes exceptionnelles")
            ->setDefaultSort(["startDate" => "DESC"])
            ->setHelp(
                Crud::PAGE_INDEX,
                "Vacances, fermetures, jours aménagés... Une période est annoncée sur le site 45 jours avant son début et disparaît automatiquement une fois terminée.",
            );
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new("label", "Intitulé")->setHelp("Ex : Vacances de Noël"),
            DateField::new("startDate", "Du")->setFormat("dd/MM/yyyy"),
            DateField::new("endDate", "Au (inclus)")->setFormat("dd/MM/yyyy"),
            BooleanField::new("closed", "Cabinet fermé")
                ->renderAsSwitch(false)
                ->setHelp("Décoché : le cabinet reste ouvert avec des jours / horaires aménagés, à préciser dans le message."),
            TextareaField::new("message", "Message (optionnel)")
                ->setNumOfRows(3)
                ->setRequired(false)
                ->setHelp("Ex : Consultations uniquement le lundi et le mardi. / Réouverture le 5 janvier.")
                ->hideOnIndex(),
            BooleanField::new("showBanner", "Bandeau sur tout le site")
                ->renderAsSwitch(false)
                ->setHelp("Affiche un bandeau en haut de toutes les pages pendant l'annonce et la période."),
        ];
    }
}
