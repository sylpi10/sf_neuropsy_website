<?php

namespace App\Controller\Admin;

use App\Entity\PageBlock;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

// Formulaire d'une section, utilisé dans la page (PageCrudController, champ "Sections") : pas d'entrée de menu
class PageBlockCrudController extends AbstractCrudController
{
    public const UPLOAD_BASE_PATH = "uploads/pages";
    public const UPLOAD_DIR = "public/uploads/pages";

    public static function getEntityFqcn(): string
    {
        return PageBlock::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new("position", "Ordre")
                ->setRequired(false)
                ->setHelp("Ordre croissant. Laisser vide : la section est ajoutée à la fin."),
            TextField::new("title", "Intertitre (optionnel)")->setRequired(false),
            TextField::new("anchor", "Ancre (optionnel)")
                ->setRequired(false)
                ->setHelp("Remplie automatiquement d'après l'intertitre à l'enregistrement. Pour un lien de menu qui descend jusqu'à cette section, recopiez-la dans le champ « Ancre » du menu."),
            TextEditorField::new("content", "Texte (optionnel)")->setRequired(false),
            ImageField::new("imageName", "Image (optionnelle)")
                ->setBasePath(self::UPLOAD_BASE_PATH)
                ->setUploadDir(self::UPLOAD_DIR)
                ->setUploadedFileNamePattern("[slug]-[ulid].[extension]")
                ->setRequired(false)
                ->setHelp("JPG, PNG ou WebP : convertie en WebP et réduite à 1200 px de large automatiquement."),
            TextField::new("imageAlt", "Description de l'image")
                ->setRequired(false)
                ->setHelp("Lue par les lecteurs d'écran. Laisser vide si l'image est purement décorative."),
            ChoiceField::new("imagePosition", "Place de l'image")
                ->setChoices([
                    "À droite du texte" => PageBlock::IMAGE_RIGHT,
                    "À gauche du texte" => PageBlock::IMAGE_LEFT,
                    "Pleine largeur, sous le texte" => PageBlock::IMAGE_FULL,
                ]),
        ];
    }
}
