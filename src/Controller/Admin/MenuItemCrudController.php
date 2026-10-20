<?php

namespace App\Controller\Admin;

use App\Entity\MenuItem;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class MenuItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuItem::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Élément de menu")
            ->setEntityLabelInPlural("Menu")
            ->setPaginatorPageSize(100)
            ->setSearchFields(null)
            ->setHelp(
                Crud::PAGE_INDEX,
                "Affiché dans l'ordre du menu. Un élément qui a des sous-éléments devient un menu déroulant.",
            );
    }

    // Liste dans l'ordre du menu : chaque élément principal suivi de ses sous-éléments
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters,
    ): QueryBuilder {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin("entity.parent", "menuParent")
            ->addSelect("COALESCE(menuParent.position, entity.position) AS HIDDEN rootPosition")
            ->addSelect("COALESCE(menuParent.id, entity.id) AS HIDDEN rootId")
            ->addSelect("CASE WHEN entity.parent IS NULL THEN 0 ELSE 1 END AS HIDDEN isChild")
            ->resetDQLPart("orderBy")
            ->orderBy("rootPosition", "ASC")
            ->addOrderBy("rootId", "ASC")
            ->addOrderBy("isChild", "ASC")
            ->addOrderBy("entity.position", "ASC")
            ->addOrderBy("entity.id", "ASC");
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new("label", "Libellé")
            ->setSortable(false)
            ->formatValue(fn ($value, MenuItem $item) => $item->getParent() ? "↳ " . $value : $value);
        yield IntegerField::new("position", "Ordre")
            ->setSortable(false)
            ->setHelp("Ordre croissant. Pour un sous-élément, l'ordre est celui dans son menu déroulant.");
        yield AssociationField::new("parent", "Dans le menu déroulant de")
            ->setSortable(false)
            ->setRequired(false)
            ->setHelp("Laisser vide pour un élément principal.")
            ->setQueryBuilder(fn (QueryBuilder $qb) => $qb
                ->andWhere("entity.parent IS NULL")
                ->orderBy("entity.position", "ASC"));
        yield BooleanField::new("visible", "Affiché")->setSortable(false);
        yield BooleanField::new("highlighted", "Mis en avant")
            ->setSortable(false)
            ->renderAsSwitch(false)
            ->hideOnIndex()
            ->setHelp("Couleur terracotta, comme « Se former ».");

        yield FormField::addFieldset("Lien")
            ->setHelp("Ignoré pour un élément qui a des sous-éléments (il ouvre alors le menu déroulant).");
        yield ChoiceField::new("targetType", "Type de lien")
            ->setSortable(false)
            ->setChoices([
                "Page du site" => MenuItem::TARGET_ROUTE,
                "Page créée dans l'admin" => MenuItem::TARGET_PAGE,
                "Adresse externe (URL)" => MenuItem::TARGET_URL,
                "Aucun (menu déroulant)" => MenuItem::TARGET_NONE,
            ])
            ->renderExpanded();
        yield ChoiceField::new("routeName", "Page du site")
            ->setChoices(array_flip(MenuItem::SYSTEM_ROUTES))
            ->setRequired(false)
            ->hideOnIndex();
        yield AssociationField::new("page", "Page créée dans l'admin")
            ->setRequired(false)
            ->hideOnIndex();
        yield TextField::new("anchor", "Ancre (optionnel)")
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp("Pour descendre directement à une section de la page, ex : bilans (sans #). Pour une section ajoutée depuis l'admin, son ancre est indiquée dans le formulaire de la section.");
        yield UrlField::new("url", "Adresse externe")
            ->setRequired(false)
            ->hideOnIndex();
    }
}
