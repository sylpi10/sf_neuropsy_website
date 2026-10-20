<?php

namespace App\Controller\Admin;

use App\Entity\OpeningDay;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

// Les 7 jours sont créés par la migration : on ne peut que les modifier
class OpeningDayCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OpeningDay::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Jour de consultation")
            ->setEntityLabelInPlural("Semaine type")
            ->setDefaultSort(["dayOfWeek" => "ASC"])
            ->setPaginatorPageSize(7)
            ->setHelp(
                Crud::PAGE_INDEX,
                "Jours de consultation habituels. Pour des vacances ou une fermeture ponctuelle, utilisez plutôt « Périodes exceptionnelles ».",
            );
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new("dayName", "Jour")->hideOnForm(),
            BooleanField::new("open", "Ouvert")->renderAsSwitch(false),
            TextField::new("hours", "Horaires (optionnel)")
                ->setRequired(false)
                ->setHelp("Ex : 9h – 18h. Laisser vide pour n'afficher que le jour."),
        ];
    }
}
