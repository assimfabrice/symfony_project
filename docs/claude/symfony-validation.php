<?php
// src/Entity/CompanyInfo.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class CompanyInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le prénom doit faire au moins {{ limit }} caractères")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le nom doit faire au moins {{ limit }} caractères")]
    private ?string $lastName = null;

    #[ORM\Column(length: 14)]
    #[Assert\NotBlank(message: "Le SIRET est obligatoire")]
    #[Assert\Length(exactly: 14, exactMessage: "Le SIRET doit faire exactement {{ limit }} caractères")]
    #[Assert\Regex(pattern: "/^[0-9]{14}$/", message: "Le SIRET doit contenir uniquement des chiffres")]
    private ?string $siret = null;

    #[ORM\Column(length: 9)]
    #[Assert\NotBlank(message: "Le SIREN est obligatoire")]
    #[Assert\Length(exactly: 9, exactMessage: "Le SIREN doit faire exactement {{ limit }} caractères")]
    #[Assert\Regex(pattern: "/^[0-9]{9}$/", message: "Le SIREN doit contenir uniquement des chiffres")]
    private ?string $siren = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas un email valide.")]
    private ?string $email = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La signature est obligatoire")]
    private ?string $signature = null;

    // Getters and setters
}

// src/Service/PdfGenerator.php
namespace App\Service;

use App\Entity\CompanyInfo;
use App\Entity\AssociateInfo;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    public function generateCompanyPdf(CompanyInfo $companyInfo): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);
        
        // Création du contenu HTML avec style
        $html = $this->getStyledCompanyTemplate($companyInfo);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }

    private function getStyledCompanyTemplate(CompanyInfo $companyInfo): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 40px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 20px;
                }
                .content {
                    margin-bottom: 40px;
                }
                .field {
                    margin-bottom: 15px;
                }
                .label {
                    font-weight: bold;
                    color: #666;
                }
                .signature {
                    margin-top: 50px;
                    border-top: 1px solid #ccc;
                    padding-top: 20px;
                }
                .date {
                    margin-top: 30px;
                    text-align: right;
                    font-style: italic;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Informations de la Société</h1>
                <p>Document généré le ' . date('d/m/Y') . '</p>
            </div>
            
            <div class="content">
                <div class="field">
                    <span class="label">Nom complet :</span>
                    <span>' . $companyInfo->getFirstName() . ' ' . $companyInfo->getLastName() . '</span>
                </div>
                
                <div class="field">
                    <span class="label">SIRET :</span>
                    <span>' . $this->formatSiret($companyInfo->getSiret()) . '</span>
                </div>
                
                <div class="field">
                    <span class="label">SIREN :</span>
                    <span>' . $this->formatSiren($companyInfo->getSiren()) . '</span>
                </div>
                
                <div class="field">
                    <span class="label">Adresse :</span>
                    <span>' . $companyInfo->getAddress() . '</span>
                </div>
                
                <div class="field">
                    <span class="label">Email :</span>
                    <span>' . $companyInfo->getEmail() . '</span>
                </div>
            </div>
            
            <div class="signature">
                <p class="label">Signature :</p>
                <img src="' . $companyInfo->getSignature() . '" width="200"/>
            </div>
            
            <div class="date">
                Document signé le ' . date('d/m/Y à H:i') . '
            </div>
        </body>
        </html>';
    }

    private function formatSiret(string $siret): string
    {
        return wordwrap($siret, 3, ' ', true);
    }

    private function formatSiren(string $siren): string
    {
        return wordwrap($siren, 3, ' ', true);
    }
}

// src/Form/CompanyInfoType.php
namespace App\Form;

use App\Entity\CompanyInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanyInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre prénom'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le prénom doit faire au moins {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre nom'
                ]
            ])
            ->add('siret', TextType::class, [
                'label' => 'SIRET',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '12345678901234',
                    'maxlength' => 14
                ],
                'help' => 'Le numéro SIRET doit contenir 14 chiffres'
            ])
            ->add('siren', TextType::class, [
                'label' => 'SIREN',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '123456789',
                    'maxlength' => 9
                ],
                'help' => 'Le numéro SIREN doit contenir 9 chiffres'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'adresse complète'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'exemple@domaine.com'
                ]
            ])
            ->add('signature', HiddenType::class, [
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyInfo::class,
        ]);
    }
}
