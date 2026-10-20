<?php

namespace App\Entity;

use App\Repository\EditableTextRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Zone de texte d'une page codée en Twig, modifiable dans l'admin ("Textes des pages").
 * La mise en page reste dans le template : {{ editable_text('cle') }} n'affiche que le texte.
 * Les zones sont créées par les migrations (pas depuis l'admin), en même temps que le template.
 */
#[ORM\Entity(repositoryClass: EditableTextRepository::class)]
#[ORM\HasLifecycleCallbacks]
class EditableText
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Identifiant utilisé dans le template, ex : formation.organisme.intro
    #[ORM\Column(name: 'text_key', length: 100, unique: true)]
    private ?string $key = null;

    // Page à laquelle appartient la zone (pour le regroupement dans l'admin)
    #[ORM\Column(length: 100)]
    private ?string $routeName = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function getPageLabel(): string
    {
        return MenuItem::SYSTEM_ROUTES[$this->routeName] ?? (string) $this->routeName;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getPosition(): int
    {
        return $this->position;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function __toString(): string
    {
        return (string) $this->label;
    }
}
