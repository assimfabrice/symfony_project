<?php

namespace App\Entity;

use App\Repository\SocieteDocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Societe;
use App\Entity\Document;

#[ORM\Entity(repositoryClass: SocieteDocumentRepository::class)]
class SocieteDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Societe::class, inversedBy: 'societeDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    private Societe $societe;

    #[ORM\ManyToOne(targetEntity: Document::class, inversedBy: 'societeDocuments')]
    #[ORM\JoinColumn(nullable: false)]
    private Document $document;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \App\Entity\Societe
     */
    public function getSociete(): \App\Entity\Societe
    {
        return $this->societe;
    }

    /**
     * @param \App\Entity\Societe $societe
     */
    public function setSociete(\App\Entity\Societe $societe): void
    {
        $this->societe = $societe;
    }

    /**
     * @return \App\Entity\Document
     */
    public function getDocument(): \App\Entity\Document
    {
        return $this->document;
    }

    /**
     * @param \App\Entity\Document $document
     */
    public function setDocument(\App\Entity\Document $document): void
    {
        $this->document = $document;
    }

}
