<?php

namespace App\Controller\Admin;

use App\Entity\MenuItem;
use App\Entity\SitePageContent;
use App\Service\PageBlockProcessor;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class SitePageContentCrudController extends AbstractCrudController
{
    public function __construct(private readonly PageBlockProcessor $pageBlockProcessor) {}

    public static function getEntityFqcn(): string
    {
        return SitePageContent::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Sections ajoutées")
            ->setEntityLabelInPlural("Sections ajoutées aux pages")
            ->setDefaultSort(["routeName" => "ASC"])
            ->setHelp(
                Crud::PAGE_INDEX,
                "Sections (texte, image) ajoutées à la fin d'une page existante du site, sous son contenu actuel.",
            );
    }

    // L'éditeur de texte est dans les sections (CollectionField) : EasyAdmin ne charge pas ses fichiers
    // tout seul dans ce cas, et le champ "Texte" s'afficherait sans zone de saisie.
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addCssFile(Asset::fromEasyAdminAssetPackage("field-text-editor.css")->onlyOnForms())
            ->addJsFile(Asset::fromEasyAdminAssetPackage("field-text-editor.js")->onlyOnForms());
    }

    public function configureActions(Actions $actions): Actions
    {
        $view = Action::new("view", "Voir la page", "fa fa-eye")
            ->linkToUrl(fn (SitePageContent $content) => $this->generateUrl($content->getRouteName()))
            ->setHtmlAttributes(["target" => "_blank"]);

        return $actions
            ->add(Crud::PAGE_INDEX, $view)
            ->add(Crud::PAGE_EDIT, $view);
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new("routeName", "Page du site")
            ->setChoices(array_flip(MenuItem::SYSTEM_ROUTES))
            ->setFormTypeOption("disabled", $pageName === Crud::PAGE_EDIT)
            ->setHelp("Une seule fiche par page : les sections s'affichent à la fin de cette page.");
        yield IntegerField::new("blocks", "Sections")
            ->formatValue(fn ($value, SitePageContent $content) => $content->getBlocks()->count())
            ->onlyOnIndex();
        yield CollectionField::new("blocks", "Sections")
            ->useEntryCrudForm(PageBlockCrudController::class)
            ->setEntryIsComplex()
            ->allowAdd()
            ->allowDelete()
            ->setHelp("Chaque section peut avoir un intertitre, un texte et une image.")
            ->onlyOnForms();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->pageBlockProcessor->process($entityInstance->getBlocks());
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->pageBlockProcessor->process($entityInstance->getBlocks());
        parent::updateEntity($entityManager, $entityInstance);
    }
}
