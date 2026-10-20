<?php

namespace App\Controller\Admin;

use App\Entity\Slide;
use App\Service\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class SlideCrudController extends AbstractCrudController
{
    private const UPLOAD_BASE_PATH = "uploads/slides";
    private const UPLOAD_DIR = "public/uploads/slides";
    private const FILE_NAME_PATTERN = "[slug]-[ulid].[extension]";
    private const MAX_WIDTH = 1920;
    private const MAX_WIDTH_MOBILE = 900;

    public function __construct(private readonly ImageOptimizer $imageOptimizer) {}

    public static function getEntityFqcn(): string
    {
        return Slide::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular("Slide")
            ->setEntityLabelInPlural("Slider page d'accueil")
            ->setDefaultSort(["position" => "ASC", "id" => "ASC"]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("id")->hideOnForm(),
            IntegerField::new("position", "Ordre d'apparition")
                ->setHelp("Les slides sont affichées par ordre croissant. La première sert de titre principal (h1) de la page."),
            ImageField::new("imageName", "Image")
                ->setBasePath(self::UPLOAD_BASE_PATH)
                ->setUploadDir(self::UPLOAD_DIR)
                ->setUploadedFileNamePattern(self::FILE_NAME_PATTERN)
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setHelp("Format paysage. JPG, PNG ou WebP : l'image est convertie en WebP et réduite à 1920 px de large automatiquement."),
            ImageField::new("mobileImageName", "Image mobile (optionnelle)")
                ->setBasePath(self::UPLOAD_BASE_PATH)
                ->setUploadDir(self::UPLOAD_DIR)
                ->setUploadedFileNamePattern(self::FILE_NAME_PATTERN)
                ->setRequired(false)
                ->setHelp("Version recadrée pour téléphone, réduite à 900 px de large automatiquement. Sinon l'image principale est utilisée.")
                ->hideOnIndex(),
            TextareaField::new("title", "Titre")
                ->setNumOfRows(2)
                ->setHelp("Un retour à la ligne est conservé à l'affichage."),
            TextareaField::new("text", "Texte")
                ->setNumOfRows(3)
                ->setRequired(false)
                ->hideOnIndex(),
        ];
    }

    public function persistEntity(
        EntityManagerInterface $entityManager,
        $entityInstance,
    ): void {
        $this->optimizeImages($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(
        EntityManagerInterface $entityManager,
        $entityInstance,
    ): void {
        $this->optimizeImages($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    // Les fichiers sont déjà déplacés dans UPLOAD_DIR par EasyAdmin à ce stade
    private function optimizeImages($entityInstance): void
    {
        if (!$entityInstance instanceof Slide) {
            return;
        }

        $uploadDir =
            $this->getParameter("kernel.project_dir") . "/" . self::UPLOAD_DIR;

        if ($entityInstance->getImageName()) {
            $entityInstance->setImageName(
                $this->imageOptimizer->optimize(
                    $uploadDir,
                    $entityInstance->getImageName(),
                    self::MAX_WIDTH,
                ),
            );
        }

        if ($entityInstance->getMobileImageName()) {
            $entityInstance->setMobileImageName(
                $this->imageOptimizer->optimize(
                    $uploadDir,
                    $entityInstance->getMobileImageName(),
                    self::MAX_WIDTH_MOBILE,
                ),
            );
        }
    }
}
