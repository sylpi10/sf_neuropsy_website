<?php

namespace App\Entity;

use App\Repository\DiplomaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Carte de la section "Diplômes" de la page Références.
 */
#[ORM\Entity(repositoryClass: DiplomaRepository::class)]
class Diploma
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    // HTML de l'éditeur (paragraphe ou liste), nettoyé à l'affichage (filtre page_html)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    // Icône "diplômé" devant le titre
    #[ORM\Column(options: ['default' => false])]
    private bool $showIcon = false;

    // Carte plus large (texte long)
    #[ORM\Column(options: ['default' => false])]
    private bool $wide = false;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function isShowIcon(): bool
    {
        return $this->showIcon;
    }

    public function setShowIcon(bool $showIcon): static
    {
        $this->showIcon = $showIcon;

        return $this;
    }

    public function isWide(): bool
    {
        return $this->wide;
    }

    public function setWide(bool $wide): static
    {
        $this->wide = $wide;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }
}
