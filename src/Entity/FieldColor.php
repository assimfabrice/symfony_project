<?php

namespace App\Entity;

use App\Repository\FieldColorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FieldColorRepository::class)]
class FieldColor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fieldName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emptyColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filledColor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function setFieldName(?string $fieldName): static
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function getEmptyColor(): ?string
    {
        return $this->emptyColor;
    }

    public function setEmptyColor(?string $emptyColor): static
    {
        $this->emptyColor = $emptyColor;

        return $this;
    }

    public function getFilledColor(): ?string
    {
        return $this->filledColor;
    }

    public function setFilledColor(?string $filledColor): static
    {
        $this->filledColor = $filledColor;

        return $this;
    }
}
