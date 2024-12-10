<?php

namespace App\Controller;
use App\Entity\CompanyType;
use App\Entity\Document;
use App\Entity\SocieteDocument;
use App\Form\AssociateCollectionType;
use App\Service\PlaceholderGenerator;
use App\Service\PdfGenerator;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Societe;
use App\Form\SocieteType;
use App\Entity\Associate;
use App\Form\AssociateType;

class WizardController extends AbstractController
{

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    //deuxième méthode
    #[Route('/wizard/step1', name: 'wizard_step1')]
    public function step1(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if(!$this->getUser()) {
            // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();

        if(count($this->getUser()->getSocietes()) > 0) {

            $idSociete = $this->getUser()->getSocietes()->toArray()[count($this->getUser()->getSocietes()->toArray()) - 1];

            $societe = $entityManager->getRepository(Societe::class)->findAssociatesByUser(
                $this->getUser(),
                $idSociete->getId(),
            );

        }else{
            $societe = new Societe();
            $societe->setUser($this->getUser()); // Associer la société à l'utilisateur
        }
        #important
        $societe = $session->get('societe', $societe);
        ##important
        $form = $this->createForm(SocieteType::class, $societe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('societe', $societe);
            return $this->redirectToRoute('wizard_step2');
        }

        return $this->render('wizard/step1.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/wizard/step2', name: 'wizard_step2')]
    public function step2(
        Request $request,
        EntityManagerInterface $entityManager,
        PdfGenerator $pdfGenerator,
    ): Response
    {
        $session = $request->getSession();
        $societe = $session->get('societe');

        if (!$societe) {
            return $this->redirectToRoute('wizard_step1');
        }

        // Charger ou initialiser les associés
        $associates = $session->get('associates', []);
        # important
        // Si aucun associé n'est présent, ajoutez-en un par défaut
        if (count($societe->getAssociates()) === 0) {
            $associate = new Associate();
            $societe->addAssociate($associate);
        }
        ## important
        $form = $this->createForm(AssociateCollectionType::class, $societe);
        $form->handleRequest($request);

        //retour aux boutons précédents
        if ($request->request->has('previous')) {
            $session->remove('associates'); // Supprimez les associés de la session
            return $this->redirectToRoute('wizard_step1');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            // Stocker les données des associés dans la session
            $session->set('societe', $societe);

            $associates = [];
            foreach ($societe->getAssociates() as $associate) {
                $associates[] = $associate;
            }

            $session->set('associates', $associates);
            foreach($associates as $associate) {
                $associate->setSociete($societe);
            }

            $companyTypeId = $societe->getCompanyType();
            //cherchons maintenant les données à la companie id corresondante
            $companyType = $entityManager->getRepository(CompanyType::class)->find($companyTypeId);
            if($this->getUser())
                $societe->setUser($this->getUser());
            $entityManager->persist($societe);
            foreach($associates as $associate) {
                $entityManager->persist($associate);
            }
            $entityManager->flush();
            //document pdf
            // Génération des PDFs
            $documents = $entityManager->getRepository(Document::class)->findBy([
                'companyType' => $companyTypeId,
            ]);

            //insertion société et document

            foreach ($documents as $document){
                $societeDocument = new SocieteDocument();
                $societeDocument->setCreatedAt(new \DateTimeImmutable('now'));
                $societeDocument->setSociete($societe);
                $societeDocument->setDocument($document);
                $entityManager->persist($societeDocument);
            }

            $entityManager->flush();
            $documentPdf = [];
            $documentTitle = [];

            foreach($documents as $document) {
                $documentPdf[] = $pdfGenerator->generateTypeSocietePdf(
                    'pdf/document_type_societe.html.twig',
                    $document,
                    $societe,
                );
                $documentTitle[] = $document->getTitle();
            }
            // Envoi des PDFs par email
            $email = (new Email())
                ->from('supprt@gmail.com')
                ->to($this->getUser()->getUserIdentifier())
                ->subject('Vos documents à télécharger')
                ->text('Veuillez trouver ci-joint vos documents.');
            // Parcourir les documents PDF générés et les attacher un par un
            foreach ($documentPdf as $index => $pdf) {
                $email->attach($pdf, $documentTitle[$index] . '.pdf');
            }

            $this->mailer->send($email);

            //redirection
            $documentTitles = array_map(fn($doc) => $doc->getTitle(), $documents);

            return $this->render('wizard/confirmation.html.twig', [
                'documents' => $documentTitles,
            ]);
        }

        return $this->render('wizard/step2.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/wizard/change-type/{type}', name: 'wizard__change_type', methods: ['POST'])]
    public function changeType(
        Request $request,
        int $type,
        SessionInterface $session,
    ): JsonResponse
    {
        // Effacer les anciennes données
        //$session->get('societe')
        $session->remove('societe');
        $session->remove('associates');
        //trouver max
        $societeByUserByCompanyTypeMaxSocieteId = $this->entityManager
            ->getRepository(Societe::class)
            ->findMaxSociete(
                $this->getUser(),
                $type,
            );

            if($societeByUserByCompanyTypeMaxSocieteId['maxSocieteId'] > 0) {
                $societeByUserByCompanyTypeMaxSocieteIdDatas = $this->entityManager
                    ->getRepository(Societe::class)
                    ->findByUserByCompany(
                        $this->getUser()->getId(),
                        $societeByUserByCompanyTypeMaxSocieteId['maxSocieteId'],
                        $type,
                    );
                $societe = array_shift($societeByUserByCompanyTypeMaxSocieteIdDatas);
                $associates = [];
                foreach ($societe->getAssociates() as $associate) {
                    $associates[] = $associate;
                }
                $session->set('societe', $societe);
                $session->set('associates', $associates);

                /** @var Societe|null  $societe */
                $dataSociete = [
                    'theNameOfTheCompany' => $societe->getTheNameOfTheCompany(),
                    'addressOfTheCompany' => $societe->getAddressOfTheCompany(),
                    'postalCode' => $societe->getPostalCode(),
                    'city' => $societe->getCity(),
                    'rcsNumber' => $societe->getRcsNumber(),
                    'siren' => $societe->getSiren(),
                    'siretOfTheCompany' => $societe->getSiretOfTheCompany(),
                    'activity' => $societe->getActivity(),
                    'nameOfTheAbsorbingCompany' => $societe->getNameOfTheAbsorbingCompany(),
                    'identificationNumber' => $societe->getIdentificationNumber(),
                    'representativeOfTheCompany' => $societe->getRepresentativeOfTheCompany(),
                    'addressOfTheLegalRepresentative' => $societe->getAddressOfTheLegalRepresentative(),
                    'postalCodeOfTheLegalRepresentative' => $societe->getPostalCodeOfTheLegalRepresentative(),
                    'cityOfTheLegalRepresentative' => $societe->getCityOfTheLegalRepresentative(),
                    'countryOfTheLegalRepresentative' => $societe->getCountryOfTheLegalRepresentative(),
                    'dateOfBirthOfTheLegalRepresentative' => $societe->getDateOfBirthOfTheLegalRepresentative()->format('Y-m-d'),
                    'nationalityOfTheLegalRepresentative' => $societe->getNationalityOfTheLegalRepresentative(),
                    'dayMonthYearOfDissolution' => $societe->getDayMonthYearOfDissolution()->format('Y-m-d'),
                    'numberOfShares' => $societe->getNumberOfShares(),
                    'amountOfAShare' => $societe->getAmountOfAShare(),
                    'amountOfPassiveSociete' => $societe->getAmountOfPassiveSociete(),
                    'amountOfActiveSociete' => $societe->getAmountOfActiveSociete(),
                    'associates' => $societe->getAssociates(),
                    'signaturePath' => $societe->getSignaturePath(),
                ];
            }else{
                $societe = new Societe();
                $associates = [];
                foreach ($societe->getAssociates() as $associate) {
                    $associates[] = $associate;
                }
                $session->set('societe', $societe);
                $session->set('associates', $associates);

                /** @var Societe|null  $societe */
                $dataSociete = [
                    'theNameOfTheCompany' => $societe->getTheNameOfTheCompany(),
                    'addressOfTheCompany' => $societe->getAddressOfTheCompany(),
                    'postalCode' => $societe->getPostalCode(),
                    'city' => $societe->getCity(),
                    'rcsNumber' => $societe->getRcsNumber(),
                    'siren' => $societe->getSiren(),
                    'siretOfTheCompany' => $societe->getSiretOfTheCompany(),
                    'activity' => $societe->getActivity(),
                    'nameOfTheAbsorbingCompany' => $societe->getNameOfTheAbsorbingCompany(),
                    'identificationNumber' => $societe->getIdentificationNumber(),
                    'representativeOfTheCompany' => $societe->getRepresentativeOfTheCompany(),
                    'addressOfTheLegalRepresentative' => $societe->getAddressOfTheLegalRepresentative(),
                    'postalCodeOfTheLegalRepresentative' => $societe->getPostalCodeOfTheLegalRepresentative(),
                    'cityOfTheLegalRepresentative' => $societe->getCityOfTheLegalRepresentative(),
                    'countryOfTheLegalRepresentative' => $societe->getCountryOfTheLegalRepresentative(),
                    'dateOfBirthOfTheLegalRepresentative' => $societe->getDateOfBirthOfTheLegalRepresentative(),
                    'nationalityOfTheLegalRepresentative' => $societe->getNationalityOfTheLegalRepresentative(),
                    'dayMonthYearOfDissolution' => $societe->getDayMonthYearOfDissolution(),
                    'numberOfShares' => $societe->getNumberOfShares(),
                    'amountOfAShare' => $societe->getAmountOfAShare(),
                    'amountOfPassiveSociete' => $societe->getAmountOfPassiveSociete(),
                    'amountOfActiveSociete' => $societe->getAmountOfActiveSociete(),
                    'associates' => $societe->getAssociates(),
                    'signaturePath' => $societe->getSignaturePath(),
                ];
            }

        return new JsonResponse($dataSociete);
    }
}