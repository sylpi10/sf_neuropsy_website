<?php

namespace App\Entity;

use App\Repository\SchedulePeriodRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Période qui modifie la semaine type : fermeture (vacances de Noël...) ou horaires aménagés.
 */
#[ORM\Entity(repositoryClass: SchedulePeriodRepository::class)]
class SchedulePeriod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startDate', message: 'La date de fin doit être après la date de début.')]
    private ?\DateTimeImmutable $endDate = null;

    // true = cabinet fermé, false = ouvert avec des jours / horaires aménagés (précisés dans le message)
    #[ORM\Column(options: ['default' => true])]
    private bool $closed = true;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $showBanner = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isShowBanner(): bool
    {
        return $this->showBanner;
    }

    public function setShowBanner(bool $showBanner): static
    {
        $this->showBanner = $showBanner;

        return $this;
    }

    public function isOngoing(\DateTimeImmutable $today = new \DateTimeImmutable('today')): bool
    {
        return $this->startDate <= $today && $this->endDate >= $today;
    }

    public function __toString(): string
    {
        return (string) $this->label;
    }
}
