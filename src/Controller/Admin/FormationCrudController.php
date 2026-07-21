<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FormationCrudController extends AbstractCrudController
{
    public function __construct(private readonly SluggerInterface $slugger) {}

    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("id")->hideOnForm(),
            TextField::new("title", "Titre"),
            NumberField::new("duration", "Durée (jours)"),
            TextField::new("startsAt", "Date de début")
                ->setHelp("Ex: Septembre 2025")
                ->setRequired(false),
            TextField::new("priceIntra", "Prix intra")->setRequired(false),
            TextField::new("priceInter", "Prix inter")->setRequired(false),
            AssociationField::new("category", "Categorie"),
            BooleanField::new("status", "Active"),
            TextField::new("fileName", "Fichier")->onlyOnIndex(),
            Field::new("file", "Fichier")
                ->setFormType(FileType::class)
                ->setFormTypeOptions([
                    "mapped" => false,
                    "required" => false,
                ])
                ->onlyOnForms(),
        ];
    }

    public function persistEntity(
        EntityManagerInterface $entityManager,
        $entityInstance,
    ): void {
        $this->handleFileUpload($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(
        EntityManagerInterface $entityManager,
        $entityInstance,
    ): void {
        $this->handleFileUpload($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleFileUpload($entityInstance): void
    {
        if (!$entityInstance instanceof Formation) {
            return;
        }

        $request = $this->getContext()?->getRequest();
        $uploadedFile = $request?->files->get("Formation")["file"] ?? null;

        if (!$uploadedFile instanceof UploadedFile) {
            return;
        }

        $originalFilename = pathinfo(
            $uploadedFile->getClientOriginalName(),
            PATHINFO_FILENAME,
        );
        $safeFilename = $this->slugger->slug($originalFilename);
        $extension =
            $uploadedFile->guessExtension() ?:
            $uploadedFile->getClientOriginalExtension() ?:
            "bin";
        $newFilename = sprintf("%s-%s.%s", $safeFilename, uniqid(), $extension);
        $uploadDir =
            $this->getParameter("kernel.project_dir") .
            "/public/uploads/formations";

        $uploadedFile->move($uploadDir, $newFilename);
        $entityInstance->setFileName($newFilename);
    }
}
