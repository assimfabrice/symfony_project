<?php

namespace App\Entity;

use App\Entity\CompanyType;

class DocumentSearch
{
    /**
     * @var int
     */
    public $page = 1;
    private ?string $title = null;
    /**
     * @var CompanyType[]
     */
    private array $companyType = [];

    /**
     * @return array
     */
    public function getCompanyType(): array
    {
        return $this->companyType;
    }

    /**
     * @param array $companyType
     */
    public function setCompanyType(array $companyType): void
    {
        $this->companyType = $companyType;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
}
