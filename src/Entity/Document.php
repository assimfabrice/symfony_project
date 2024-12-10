<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?array $fields = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphe = null;

    #[ORM\Column(nullable: true)]
    private ?array $paragraphes = null;

    #[ORM\ManyToOne(inversedBy: 'documents',cascade: ['persist'])]
    private ?CompanyType $companyType = null;

    #[ORM\OneToMany(targetEntity: SocieteDocument::class, mappedBy: 'document', cascade: ['persist', 'remove'])]
    private Collection $societeDocuments;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function setFields(?array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function getParagraphe(): ?string
    {
        return $this->paragraphe;
    }

    public function setParagraphe(?string $paragraphe): static
    {
        $this->paragraphe = $paragraphe;

        return $this;
    }

    public function getParagraphes(): ?array
    {
        return $this->paragraphes;
    }

    public function setParagraphes(?array $paragraphes): static
    {
        $this->paragraphes = $paragraphes;

        return $this;
    }
    public function getCompanyType(): ?CompanyType
    {
        return $this->companyType;
    }

    public function setCompanyType(?CompanyType $companyType): static
    {
        $this->companyType = $companyType;

        return $this;
    }

}