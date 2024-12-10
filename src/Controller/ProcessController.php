<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Societe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Associate;
use App\Form\SocieteType;
use App\Form\AssociateType;
use App\Service\PdfGenerator;
use Symfony\Component\Mime\Email;
#[Route('process', name: 'app_process_')]
class ProcessController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    /**
     * @param Request $request
     * @param int $step
     * @param PdfGenerator $pdfGenerator
     * @return Response
     */
    #[Route('/{step}', name: 'index', defaults: ['step' => 1])]
    public function index(
        Request $request,
        int $step,
        PdfGenerator $pdfGenerator,
        MailerInterface $mailer,
    ): Response
    {
        $wizardSociete = $request->getSession()->get('wizard_societe');
        $wizardAssociate = $request->getSession()->get('wizard_associate');

        if(!$wizardSociete && $step === 2 ){
            return $this->redirectToRoute('app_process_index', ['step' => $step - 1]);
        }

        //dump($wizardSociete, $wizardAssociate);
        if(!$wizardSociete) {
            $wizardSociete = new Societe();

        }
        if(!$wizardAssociate) {
            $wizardAssociate = new Associate();
        }

        // Déterminer le formulaire à afficher selon l'étape
        $form = match ($step) {
            1 => $this->createForm(SocieteType::class, $wizardSociete),
            2 => $this->createForm(AssociateType::class, $wizardAssociate),
            default => throw $this->createNotFoundException('Étape invalide'),
        };

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Stocker les données dans la session
            $request->getSession()->set('wizard_societe', $wizardSociete);
            $request->getSession()->set('wizard_associate', $wizardAssociate);

            $signatureDataSociete = $request->request->get('signature-societe');
            $signatureDataAssociate = $request->request->get('signature-associate');

            $signaturePathSociete = '';
            $signaturePathAssociate = '';
            if ($signatureDataSociete) {
                // Extraire les données de l'image (base64)
                list(, $signatureDataSociete) = explode(',', $signatureDataSociete);

                $signatureDataSociete = base64_decode($signatureDataSociete);
                // Sauvegarder l'image de la signature dans un fichier temporaire ou en base de données

                $signaturePathSociete = './pdf/signature_societe_' . $wizardSociete->getRcsNumber() . '.png';
                file_put_contents($signaturePathSociete, $signatureDataSociete);

            }

            if ($signatureDataAssociate) {
                // Extraire les données de l'image (base64)
                list(, $signatureDataAssociate) = explode(',', $signatureDataAssociate);

                $signatureDataAssociate = base64_decode($signatureDataAssociate);
                // Sauvegarder l'image de la signature dans un fichier temporaire ou en base de données

                $signaturePathAssociate = './pdf/signature_associate_' . $wizardAssociate->getLastname() . '.png';
                file_put_contents($signaturePathAssociate, $signatureDataAssociate);

            }

            // Si c'est la dernière étape et qu'on clique sur "Envoyer"
            if ($step === 2 && $request->request->has('envoyer'))
            {

                $wizardSociete->setSignaturePath($signaturePathSociete);
                $wizardAssociate->setSociete($wizardSociete);
                $this->entityManager->persist($wizardSociete);

                $wizardAssociate->setSignaturePath($signaturePathAssociate);
                $this->entityManager->persist($wizardAssociate);

                $this->entityManager->flush();

                $lastSocieteIdInsert = $wizardSociete->getId();
                $lastAssociateIdInsert = $wizardAssociate->getId();

                $societe = $this->entityManager->getRepository(Societe::class)->find($lastSocieteIdInsert);
                $associate = $this->entityManager->getRepository(Associate::class)->find($lastAssociateIdInsert);

                if (!$societe) {
                    throw $this->createNotFoundException('Société non trouvée');
                }

                // Génération des PDFs
                $societePdf = $pdfGenerator->generateSocietePdf('pdf/document_societe.html.twig', $societe);
                $associatePdf = $pdfGenerator->generateAssociatePdf('pdf/document_associate.html.twig', $associate);
                //document
                $typeSociete = $societe->getTheTypeOfCompany();
                $document = $this->entityManager->getRepository(Document::class)->findBy([
                    'typeSociete' => $typeSociete
                ]);

                $document = array_shift($document);
                $documentPdf = '';
                if($typeSociete === $document->getTypeSociete()) {
                    $documentPdf = $pdfGenerator->generateTypeSocietePdf('pdf/document_type_societe.html.twig', $document, $societe);
                }


                //fin document
                // Envoi des PDFs par email
                $email = (new Email())
                    ->from('supprt@gmail.com')
                    ->to('fabassim@gmail.com')
                    ->subject('Vos documents')
                    ->text('Veuillez trouver ci-joint vos documents.')
                    ->attach($documentPdf, 'typesociete@gmail.com' . '.pdf')
                    ->attach($societePdf, 'fabassim@gmail.com' . '.pdf')
                    ->attach($associatePdf, 'associate@gmail.com' . '.pdf');


                $mailer->send($email);

                /*$request->getSession()->remove('wizard_societe');
                $request->getSession()->remove('wizard_associate');*/

            }
            // Navigation entre les étapes
            if ($request->request->has('suivant') && $step < 2) {
                return $this->redirectToRoute('app_process_index', ['step' => $step + 1]);
            }
            if ($request->request->has('precedent') && $step > 1) {
                return $this->redirectToRoute('app_process_index', ['step' => $step - 1]);
            }
        }
        return $this->render('home/step_one.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
        ]);
    }
}