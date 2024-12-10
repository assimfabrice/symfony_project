<?php
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
    #[Route('/formulaire/etape1', name: 'form_step_one')]
    public function stepOne(Request $request, EntityManagerInterface $em): Response
    {
        // Création d'une nouvelle instance de CompanyInfo
        $companyInfo = new CompanyInfo();
        $form = $this->createForm(CompanyInfoType::class, $companyInfo);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Exemple de traitement d'une signature en base64
            $signature = $request->request->get('signature');
            $companyInfo->setSignature($signature);
            
            $em->persist($companyInfo);
            $em->flush();
            
            // Stockage de l'ID dans la session pour l'étape suivante
            $request->getSession()->set('company_id', $companyInfo->getId());
            
            return $this->redirectToRoute('form_step_two');
        }

        return $this->render('form/step_one.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/formulaire/etape2', name: 'form_step_two')]
    public function stepTwo(Request $request, EntityManagerInterface $em, PdfGenerator $pdfGenerator, MailerInterface $mailer): Response
    {
        // Récupération de la société depuis la session
        $companyId = $request->getSession()->get('company_id');
        $companyInfo = $em->getRepository(CompanyInfo::class)->find($companyId);
        
        if (!$companyInfo) {
            return $this->redirectToRoute('form_step_one');
        }

        $associateInfo = new AssociateInfo();
        $form = $this->createForm(AssociateInfoType::class, $associateInfo);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Association avec la société
            $associateInfo->setCompanyInfo($companyInfo);
            
            // Traitement de la signature
            $signature = $request->request->get('signature');
            $associateInfo->setSignature($signature);
            
            $em->persist($associateInfo);
            $em->flush();

            // Génération des PDFs
            $companyPdf = $pdfGenerator->generateCompanyPdf($companyInfo);
            $associatePdf = $pdfGenerator->generateAssociatePdf($associateInfo);

            // Envoi des emails avec les PDFs
            $this->sendDocuments($mailer, $companyInfo, $associateInfo, $companyPdf, $associatePdf);

            // Nettoyage de la session
            $request->getSession()->remove('company_id');

            return $this->redirectToRoute('form_success');
        }

        return $this->render('form/step_two.html.twig', [
            'form' => $form->createView(),
            'company' => $companyInfo,
        ]);
    }

    private function sendDocuments(
        MailerInterface $mailer, 
        CompanyInfo $company, 
        AssociateInfo $associate,
        string $companyPdf,
        string $associatePdf
    ): void {
        // Email pour la société
        $emailCompany = (new Email())
            ->from('no-reply@votreentreprise.fr')
            ->to($company->getEmail())
            ->subject('Vos documents de souscription')
            ->html($this->renderView('emails/documents.html.twig', [
                'name' => $company->getFirstName(),
                'type' => 'société'
            ]))
            ->attach($companyPdf, sprintf('societe-%s.pdf', $company->getEmail()))
            ->attach($associatePdf, sprintf('associe-%s.pdf', $associate->getEmail()));

        // Email pour l'associé
        $emailAssociate = (new Email())
            ->from('no-reply@votreentreprise.fr')
            ->to($associate->getEmail())
            ->subject('Vos documents de souscription')
            ->html($this->renderView('emails/documents.html.twig', [
                'name' => $associate->getFirstName(),
                'type' => 'associé'
            ]))
            ->attach($companyPdf, sprintf('societe-%s.pdf', $company->getEmail()))
            ->attach($associatePdf, sprintf('associe-%s.pdf', $associate->getEmail()));

        $mailer->send($emailCompany);
        $mailer->send($emailAssociate);
    }

    #[Route('/formulaire/succes', name: 'form_success')]
    public function success(): Response
    {
        return $this->render('form/success.html.twig');
    }
}
