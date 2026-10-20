<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Service\ImageOptimizer;
use App\Service\PageBlockProcessor;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PageCrudController extends AbstractCrudController
{
    private const HERO_MAX_WIDTH = 1920;

    public function __construct(
        private readonly ImageOptimizer $imageOptimizer,
        private readonly PageBlockProcessor $pageBlockProcessor,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Page")
            ->setEntityLabelInPlural("Pages créées")
            ->setDefaultSort(["title" => "ASC"])
            ->setHelp(
                Crud::PAGE_INDEX,
                "Pages créées depuis l'admin. Pour les afficher dans le menu, ajoutez un élément dans « Menu ».",
            );
    }

    public function configureActions(Actions $actions): Actions
    {
        $view = Action::new("view", "Voir la page", "fa fa-eye")
            ->linkToUrl(fn (Page $page) => $this->generateUrl("app_page", ["slug" => $page->getSlug()]))
            ->setHtmlAttributes(["target" => "_blank"]);

        return $actions
            ->add(Crud::PAGE_INDEX, $view)
            ->add(Crud::PAGE_EDIT, $view);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab("Contenu");
        yield TextField::new("title", "Titre (h1)");
        yield SlugField::new("slug", "Adresse")
            ->setTargetFieldName("title")
            ->setHelp("La page sera en ligne sur /adresse. Minuscules, chiffres et tirets uniquement.")
            ->formatValue(fn ($value) => "/" . $value);
        yield BooleanField::new("published", "Publiée")
            ->renderAsSwitch(false)
            ->setHelp("Non publiée : visible uniquement par vous (connectée à l'admin), via « Voir la page ».");
        yield ImageField::new("heroImageName", "Image du bandeau")
            ->setBasePath(PageBlockCrudController::UPLOAD_BASE_PATH)
            ->setUploadDir(PageBlockCrudController::UPLOAD_DIR)
            ->setUploadedFileNamePattern("[slug]-[ulid].[extension]")
            ->setRequired(false)
            ->setHelp("Format paysage. Convertie en WebP et réduite à 1920 px automatiquement. Sans image, le bandeau par défaut est utilisé.")
            ->hideOnIndex();
        yield CollectionField::new("blocks", "Sections")
            ->useEntryCrudForm(PageBlockCrudController::class)
            ->setEntryIsComplex()
            ->allowAdd()
            ->allowDelete()
            ->setHelp("Chaque section peut avoir un intertitre, un texte et une image.")
            ->onlyOnForms();

        yield FormField::addTab("Référencement");
        yield TextField::new("seoTitle", "Titre Google")
            ->setRequired(false)
            ->setHelp("~60 caractères. Vide : « Titre de la page – Emmanuelle Mir ».")
            ->hideOnIndex();
        yield TextareaField::new("seoDescription", "Description Google")
            ->setRequired(false)
            ->setNumOfRows(3)
            ->setHelp("~150 caractères, affichée sous le titre dans les résultats Google.")
            ->hideOnIndex();

        yield DateTimeField::new("updatedAt", "Modifiée le")
            ->setFormat("dd/MM/yyyy HH:mm")
            ->onlyOnIndex();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->prepare($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->prepare($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    // Les fichiers sont déjà déplacés dans UPLOAD_DIR par EasyAdmin à ce stade
    private function prepare($page): void
    {
        if (!$page instanceof Page) {
            return;
        }

        if ($page->getHeroImageName()) {
            $uploadDir = $this->getParameter("kernel.project_dir") . "/" . PageBlockCrudController::UPLOAD_DIR;
            $page->setHeroImageName(
                $this->imageOptimizer->optimize($uploadDir, $page->getHeroImageName(), self::HERO_MAX_WIDTH),
            );
        }

        $this->pageBlockProcessor->process($page->getBlocks());
        // Force la mise à jour de updatedAt (sitemap) même si seules les sections ont changé
        $page->touch();
    }
}
