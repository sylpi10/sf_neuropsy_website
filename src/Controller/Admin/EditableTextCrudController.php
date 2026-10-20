<?php

namespace App\Controller\Admin;

use App\Entity\EditableText;
use App\Entity\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

// Les zones sont créées par les migrations, avec le template : ici on ne peut que modifier leur texte
class EditableTextCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EditableText::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Texte")
            ->setEntityLabelInPlural("Textes des pages")
            ->setDefaultSort(["routeName" => "ASC", "position" => "ASC"])
            ->setPaginatorPageSize(50)
            ->setSearchFields(["label", "content"])
            ->setPageTitle(Crud::PAGE_EDIT, fn (EditableText $text) => $text->getPageLabel() . " → " . $text->getLabel())
            ->setHelp(
                Crud::PAGE_INDEX,
                "Textes des pages du site, dans l'ordre de la page. Les titres et la mise en page ne sont pas modifiables ici.",
            );
    }

    public function configureActions(Actions $actions): Actions
    {
        $view = Action::new("view", "Voir la page", "fa fa-eye")
            ->linkToUrl(fn (EditableText $text) => $this->generateUrl($text->getRouteName()))
            ->setHtmlAttributes(["target" => "_blank"]);

        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, $view)
            ->add(Crud::PAGE_EDIT, $view);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(
            ChoiceFilter::new("routeName", "Page")->setChoices(array_flip(MenuItem::SYSTEM_ROUTES)),
        );
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new("routeName", "Page")
            ->setChoices(array_flip(MenuItem::SYSTEM_ROUTES))
            ->onlyOnIndex();
        yield TextField::new("label", "Emplacement")->onlyOnIndex();
        yield TextField::new("content", "Début du texte")
            ->formatValue(fn ($value) => mb_strimwidth(trim(html_entity_decode(strip_tags((string) $value))), 0, 90, "…"))
            ->onlyOnIndex();
        yield TextEditorField::new("content", "Texte")
            ->setNumOfRows(12)
            ->setHelp("Gras, italique, listes et liens. Un retour à la ligne crée un nouveau paragraphe.")
            ->onlyOnForms();
    }
}
