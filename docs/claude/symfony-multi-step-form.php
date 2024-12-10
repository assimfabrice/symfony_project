<?php
// src/Entity/CompanyInfo.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CompanyInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 14)]
    private ?string $siret = null;

    #[ORM\Column(length: 9)]
    private ?string $siren = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'text')]
    private ?string $signature = null;

    // Getters and setters...
}

// src/Entity/AssociateInfo.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AssociateInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'text')]
    private ?string $signature = null;

    #[ORM\ManyToOne(targetEntity: CompanyInfo::class)]
    private ?CompanyInfo $companyInfo = null;

    // Getters and setters...
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

class CompanyInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('siret', TextType::class, ['label' => 'SIRET'])
            ->add('siren', TextType::class, ['label' => 'SIREN'])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('signature', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyInfo::class,
        ]);
    }
}

// src/Form/AssociateInfoType.php
namespace App\Form;

use App\Entity\AssociateInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('signature', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssociateInfo::class,
        ]);
    }
}

// src/Service/PdfGenerator.php
namespace App\Service;

use App\Entity\CompanyInfo;
use App\Entity\AssociateInfo;
use Dompdf\Dompdf;

class PdfGenerator
{
    public function generateCompanyPdf(CompanyInfo $companyInfo): string
    {
        $dompdf = new Dompdf();
        $html = $this->renderCompanyTemplate($companyInfo);
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->output();
    }

    public function generateAssociatePdf(AssociateInfo $associateInfo): string
    {
        $dompdf = new Dompdf();
        $html = $this->renderAssociateTemplate($associateInfo);
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        return $dompdf->output();
    }

    private function renderCompanyTemplate(CompanyInfo $companyInfo): string
    {
        // Template HTML pour le PDF de la société
        return "
            <h1>Informations de la société</h1>
            <p>Nom: {$companyInfo->getLastName()}</p>
            <p>Prénom: {$companyInfo->getFirstName()}</p>
            <p>SIRET: {$companyInfo->getSiret()}</p>
            <p>SIREN: {$companyInfo->getSiren()}</p>
            <p>Adresse: {$companyInfo->getAddress()}</p>
            <p>Email: {$companyInfo->getEmail()}</p>
            <img src='{$companyInfo->getSignature()}' alt='Signature' />
        ";
    }

    private function renderAssociateTemplate(AssociateInfo $associateInfo): string
    {
        // Template HTML pour le PDF de l'associé
        return "
            <h1>Informations de l'associé</h1>
            <p>Nom: {$associateInfo->getLastName()}</p>
            <p>Prénom: {$associateInfo->getFirstName()}</p>
            <p>Adresse: {$associateInfo->getAddress()}</p>
            <p>Email: {$associateInfo->getEmail()}</p>
            <img src='{$associateInfo->getSignature()}' alt='Signature' />
        ";
    }
}

// src/Controller/FormController.php
namespace App\Controller;

use App\Entity\CompanyInfo;
use App\Entity\AssociateInfo;
use App\Form\CompanyInfoType;
use App\Form\AssociateInfoType;
use App\Service\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class FormController extends AbstractController
{
    #[Route('/', name: 'step_one')]
    public function stepOne(Request $request, EntityManagerInterface $em): Response
    {
        $companyInfo = new CompanyInfo();
        $form = $this->createForm(CompanyInfoType::class, $companyInfo);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($companyInfo);
            $em->flush();
            
            return $this->redirectToRoute('step_two', ['company_id' => $companyInfo->getId()]);
        }

        return $this->render('form/step_one.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/step-two/{company_id}', name: 'step_two')]
    public function stepTwo(
        Request $request,
        EntityManagerInterface $em,
        int $company_id,
        PdfGenerator $pdfGenerator,
        MailerInterface $mailer
    ): Response {
        $companyInfo = $em->getRepository(CompanyInfo::class)->find($company_id);
        
        if (!$companyInfo) {
            throw $this->createNotFoundException('Société non trouvée');
        }

        $associateInfo = new AssociateInfo();
        $associateInfo->setCompanyInfo($companyInfo);
        $form = $this->createForm(AssociateInfoType::class, $associateInfo);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($associateInfo);
            $em->flush();

            // Génération des PDFs
            $companyPdf = $pdfGenerator->generateCompanyPdf($companyInfo);
            $associatePdf = $pdfGenerator->generateAssociatePdf($associateInfo);

            // Envoi des PDFs par email
            $email = (new Email())
                ->from('votre@email.com')
                ->to($companyInfo->getEmail())
                ->subject('Vos documents')
                ->text('Veuillez trouver ci-joint vos documents.')
                ->attach($companyPdf, $companyInfo->getEmail() . '.pdf')
                ->attach($associatePdf, $associateInfo->getEmail() . '.pdf');

            $mailer->send($email);

            return $this->redirectToRoute('success');
        }

        return $this->render('form/step_two.html.twig', [
            'form' => $form->createView(),
            'company_info' => $companyInfo,
        ]);
    }
}
