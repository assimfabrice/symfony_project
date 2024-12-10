<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: SocieteRepository::class)]
class Societe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la société est obligatoire")]
    private ?string $theNameOfTheCompany = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse de la société' est obligatoire")]
    private ?string $addressOfTheCompany = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le code postal est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Le code postal doit contenir uniquement des chiffres positive")]
    #[Assert\Positive]
    private ?int $postalCode = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la ville est obligatoire")]
    private ?string $city = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numero rcs est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]+$/", message: "Le rcs doit contenir uniquement des chiffres et contenir 9 chiffres")]
    #[Assert\Positive]
    private ?int $rcsNumber = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numero siren est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]+$/", message: "Le numéro siren doit contenir uniquement des chiffres")]
    #[Assert\Positive]
    private ?int $siren = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numero de siret est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Le numéro de siret doit contenir uniquement des chiffres")]
    #[Assert\Positive]
    private ?int $siretOfTheCompany = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'activité' est obligatoire")]
    private ?string $activity = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $nameOfTheAbsorbingCompany = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Ce champ doit contenir uniquement des chiffres")]
    private ?int $IdentificationNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $representativeOfTheCompany = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $AddressOfTheLegalRepresentative = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Ce champ doit contenir uniquement des chiffres")]
    private ?int $postalCodeOfTheLegalRepresentative = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $cityOfTheLegalRepresentative = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $countryOfTheLegalRepresentative = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?\DateTimeInterface $dateOfBirthOfTheLegalRepresentative = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $nationalityOfTheLegalRepresentative = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?\DateTimeInterface $dayMonthYearOfDissolution = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Ce champ doit contenir uniquement des chiffres")]
    private ?int $numberOfShares = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Ce champ doit contenir uniquement des nombres")]
    private ?string $amountOfAShare = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Ce champ doit contenir uniquement des nombres")]
    private ?string $amountOfPassiveSociete = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    #[Assert\Regex(pattern: "/^[0-9]{1,}$/", message: "Ce champ doit contenir uniquement des nombres")]
    private ?string $amountOfActiveSociete = null;


    /**
     * @var Collection<int, Associate>
     */
    #[ORM\OneToMany(targetEntity: Associate::class, mappedBy: 'societe', cascade: ['persist', 'remove'])]
    private Collection $associates;

    #[ORM\Column(length: 100000)]
    private ?string $signaturePath = null;

    #[ORM\Column(length: 255)]
    private ?string $companyType = null;

    #[ORM\OneToMany(targetEntity: SocieteDocument::class, mappedBy: 'societe', cascade: ['persist', 'remove'])]
    private Collection $societeDocuments;

    #[ORM\ManyToOne(inversedBy: 'societes')]
    private ?User $user = null;


    public function __construct()
    {
        $this->associates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheNameOfTheCompany(): ?string
    {
        return $this->theNameOfTheCompany;
    }

    public function setTheNameOfTheCompany(string $theNameOfTheCompany): static
    {
        $this->theNameOfTheCompany = $theNameOfTheCompany;

        return $this;
    }

    public function getAddressOfTheCompany(): ?string
    {
        return $this->addressOfTheCompany;
    }

    public function setAddressOfTheCompany(string $addressOfTheCompany): static
    {
        $this->addressOfTheCompany = $addressOfTheCompany;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRcsNumber(): ?int
    {
        return $this->rcsNumber;
    }

    public function setRcsNumber(int $rcsNumber): static
    {
        $this->rcsNumber = $rcsNumber;

        return $this;
    }

    public function getSiren(): ?int
    {
        return $this->siren;
    }

    public function setSiren(int $siren): static
    {
        $this->siren = $siren;

        return $this;
    }

    public function getSiretOfTheCompany(): ?int
    {
        return $this->siretOfTheCompany;
    }

    public function setSiretOfTheCompany(int $siretOfTheCompany): static
    {
        $this->siretOfTheCompany = $siretOfTheCompany;

        return $this;
    }

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(string $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    public function getNameOfTheAbsorbingCompany(): ?string
    {
        return $this->nameOfTheAbsorbingCompany;
    }

    public function setNameOfTheAbsorbingCompany(string $nameOfTheAbsorbingCompany): static
    {
        $this->nameOfTheAbsorbingCompany = $nameOfTheAbsorbingCompany;

        return $this;
    }

    public function getIdentificationNumber(): ?int
    {
        return $this->IdentificationNumber;
    }

    public function setIdentificationNumber(int $IdentificationNumber): static
    {
        $this->IdentificationNumber = $IdentificationNumber;

        return $this;
    }

    public function getRepresentativeOfTheCompany(): ?string
    {
        return $this->representativeOfTheCompany;
    }

    public function setRepresentativeOfTheCompany(string $representativeOfTheCompany): static
    {
        $this->representativeOfTheCompany = $representativeOfTheCompany;

        return $this;
    }

    public function getAddressOfTheLegalRepresentative(): ?string
    {
        return $this->AddressOfTheLegalRepresentative;
    }

    public function setAddressOfTheLegalRepresentative(string $AddressOfTheLegalRepresentative): static
    {
        $this->AddressOfTheLegalRepresentative = $AddressOfTheLegalRepresentative;

        return $this;
    }

    public function getPostalCodeOfTheLegalRepresentative(): ?int
    {
        return $this->postalCodeOfTheLegalRepresentative;
    }

    public function setPostalCodeOfTheLegalRepresentative(int $postalCodeOfTheLegalRepresentative): static
    {
        $this->postalCodeOfTheLegalRepresentative = $postalCodeOfTheLegalRepresentative;

        return $this;
    }

    public function getCityOfTheLegalRepresentative(): ?string
    {
        return $this->cityOfTheLegalRepresentative;
    }

    public function setCityOfTheLegalRepresentative(string $cityOfTheLegalRepresentative): static
    {
        $this->cityOfTheLegalRepresentative = $cityOfTheLegalRepresentative;

        return $this;
    }

    public function getCountryOfTheLegalRepresentative(): ?string
    {
        return $this->countryOfTheLegalRepresentative;
    }

    public function setCountryOfTheLegalRepresentative(string $countryOfTheLegalRepresentative): static
    {
        $this->countryOfTheLegalRepresentative = $countryOfTheLegalRepresentative;

        return $this;
    }

    public function getDateOfBirthOfTheLegalRepresentative(): ?\DateTimeInterface
    {
        return $this->dateOfBirthOfTheLegalRepresentative;
    }

    public function setDateOfBirthOfTheLegalRepresentative(\DateTimeInterface $dateOfBirthOfTheLegalRepresentative): static
    {
        $this->dateOfBirthOfTheLegalRepresentative = $dateOfBirthOfTheLegalRepresentative;

        return $this;
    }

    public function getNationalityOfTheLegalRepresentative(): ?string
    {
        return $this->nationalityOfTheLegalRepresentative;
    }

    public function setNationalityOfTheLegalRepresentative(string $nationalityOfTheLegalRepresentative): static
    {
        $this->nationalityOfTheLegalRepresentative = $nationalityOfTheLegalRepresentative;

        return $this;
    }

    public function getDayMonthYearOfDissolution(): ?\DateTimeInterface
    {
        return $this->dayMonthYearOfDissolution;
    }

    public function setDayMonthYearOfDissolution(\DateTimeInterface $dayMonthYearOfDissolution): static
    {
        $this->dayMonthYearOfDissolution = $dayMonthYearOfDissolution;

        return $this;
    }

    public function getNumberOfShares(): ?int
    {
        return $this->numberOfShares;
    }

    public function setNumberOfShares(int $numberOfShares): static
    {
        $this->numberOfShares = $numberOfShares;

        return $this;
    }

    public function getAmountOfAShare(): ?string
    {
        return $this->amountOfAShare;
    }

    public function setAmountOfAShare(string $amountOfAShare): static
    {
        $this->amountOfAShare = $amountOfAShare;

        return $this;
    }

    public function getAmountOfPassiveSociete(): ?string
    {
        return $this->amountOfPassiveSociete;
    }

    public function setAmountOfPassiveSociete(string $amountOfPassiveSociete): static
    {
        $this->amountOfPassiveSociete = $amountOfPassiveSociete;

        return $this;
    }

    public function getAmountOfActiveSociete(): ?string
    {
        return $this->amountOfActiveSociete;
    }

    public function setAmountOfActiveSociete(string $amountOfActiveSociete): static
    {
        $this->amountOfActiveSociete = $amountOfActiveSociete;

        return $this;
    }


    /**
     * @return Collection<int, Associate>
     */
    public function getAssociates(): Collection
    {
        return $this->associates;
    }

    public function addAssociate(Associate $associate): static
    {
        if (!$this->associates->contains($associate)) {
            $this->associates->add($associate);
            $associate->setSociete($this);
        }

        return $this;
    }

    public function removeAssociate(Associate $associate): static
    {
        if ($this->associates->removeElement($associate)) {
            // set the owning side to null (unless already changed)
            if ($associate->getSociete() === $this) {
                $associate->setSociete(null);
            }
        }

        return $this;
    }

    public function getSignaturePath(): ?string
    {
        return $this->signaturePath;
    }

    public function setSignaturePath(string $signaturePath): static
    {
        $this->signaturePath = $signaturePath;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getTheNameOfTheCompany();
    }

    public function getCompanyType(): ?string
    {
        return $this->companyType;
    }

    public function setCompanyType(string $companyType): static
    {
        $this->companyType = $companyType;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
