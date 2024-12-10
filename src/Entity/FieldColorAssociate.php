<?php

namespace App\Entity;

use App\Repository\FieldColorAssociateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FieldColorAssociateRepository::class)]
class FieldColorAssociate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $emptyColor = null;

    #[ORM\Column(length: 255)]
    private ?string $filledColor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmptyColor(): ?string
    {
        return $this->emptyColor;
    }

    public function setEmptyColor(string $emptyColor): static
    {
        $this->emptyColor = $emptyColor;

        return $this;
    }

    public function getFilledColor(): ?string
    {
        return $this->filledColor;
    }

    public function setFilledColor(string $filledColor): static
    {
        $this->filledColor = $filledColor;

        return $this;
    }
}
