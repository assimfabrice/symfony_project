<?php

namespace App\Controller;
use App\Entity\Associate;
use App\Entity\CompanyType;
use App\Entity\DocumentSearch;
use App\Entity\Societe;
use App\Form\CompanyTypeFormType;
use App\Form\DocumentSearchType;
use App\Repository\CompanyTypeRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Document;
use App\Form\DocumentType;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('admin/document', name: 'app_document_')]
#[IsGranted(User::ROLE_ADMIN)]
class DocumentController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/new', name: 'new')]
    public function new(
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute('app_document_index');
        }


        return $this->render('document/form.html.twig', [
            'form' => $form->createView(),
            'placeholders' => $this->showEntityPlaceholders($em->getRepository(Societe::class)),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['page' => '\d+'])]
    public function edit(
        Document $document,
        Request $request,
        EntityManagerInterface $em,
        int $id
    ): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $document = $em->getRepository(Document::class)->find($id);
        //dd($document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_document_index');
        }
        return $this->render('document/form.html.twig', [
            'form' => $form->createView(),
            'placeholders' => $this->showEntityPlaceholders($em->getRepository(Societe::class)),
            'id' => $id,
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(
        EntityManagerInterface $em,
        Request $request,
        PaginatorInterface $paginator,
    ): Response
    {
        $companyTypes = $em-> getRepository(CompanyType::class)->findAll();
        $dataDocumentSearch = new DocumentSearch();
        $dataDocumentSearch->page = $request->get('page', 1);
        $formSearchDocument = $this->createForm(DocumentSearchType::class, $dataDocumentSearch);
        $formSearchDocument->handleRequest($request);

        $documents = $em->getRepository(Document::class)->findSearch($dataDocumentSearch);

        //$documents = $em->getRepository(Document::class)->findAll();
        /*$documents = $paginator->paginate(
            $em->getRepository(Document::class)->findAll(),
            $request->query->getInt('page', 1),
            5
        );*/
        // Gestion de l'ajout ou modification d'une company type
        $companyType = new CompanyType();
        $companyTypeForm = $this->createForm(CompanyTypeFormType::class, $companyType);
        $companyTypeForm->handleRequest($request);

        if ($companyTypeForm->isSubmitted() && $companyTypeForm->isValid()) {
            $em->persist($companyType);
            $em->flush();
            return $this->redirectToRoute('app_document_index');
        }
        return $this->render('document/index.html.twig', [
            'documents' => $documents,
            'companyTypes' => $companyTypes,
            'companyTypeForm' => $companyTypeForm->createView(),
            'formSearchDocument' => $formSearchDocument->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Document $document, EntityManagerInterface $em): Response
    {
        $em->remove($document);
        $em->flush();

        return $this->redirectToRoute('app_document_index');
    }
    #[Route('/search', name: 'search')]
    public function search(
        Request $request,
        DocumentRepository $documentRepository,
        PaginatorInterface $paginator
    ): Response {
        $searchForm = $this->createForm(DocumentSearchType::class);
        $searchForm->handleRequest($request);

        $criteria = $searchForm->getData() ?? [];

        $queryBuilder = $documentRepository->findBySearchCriteria($criteria);

        // Gestion du tri
        $sort = $request->query->get('sort', 'id');
        $direction = $request->query->get('direction', 'asc');

        switch($sort) {
            case 'companyType':
                $queryBuilder->orderBy('ct.name', $direction);
                break;
            case 'societe':
                $queryBuilder->orderBy('s.theNameOfTheCompany', $direction);
                break;
            default:
                $queryBuilder->orderBy('d.'.$sort, $direction);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('document/search.html.twig', [
            'pagination' => $pagination,
            'searchForm' => $searchForm->createView()
        ]);
    }
    private function showEntityPlaceholders(EntityRepository $entity): array
    {
        $placeholders = [];
        $className = $entity->getClassName();

        foreach (get_class_methods($className) as $method) {
            if (strpos($method, '__') === 0 || strpos($method, 'get') !== 0) {
                continue;
            }

            // Obtenir le nom de la propriété
            $propertyName = lcfirst(substr($method, 3));

            // Formater avec des accolades et un point d'interrogation
            $alias = '{' . $method . '?}';

            // Traduire la propriété
            $translatedPlaceholder = $this->translator->trans('placeholders.' . $propertyName, [], 'messages');

            // Si la traduction échoue, utiliser un fallback
            if ($translatedPlaceholder === 'placeholders.' . $propertyName) {
                $translatedPlaceholder = ucfirst($propertyName); // Nom brut en fallback
            }

            // Ajouter au tableau des placeholders
            $placeholders[$method] = [
                'method' => $method,           // Nom brut de la méthode (ex: getPostalCode)
                'alias' => $alias,             // Format {getPostalCode?}
                'translated' => $translatedPlaceholder, // Traduction ou fallback
            ];
        }

        return $placeholders;
    }
    #[Route('/api/company-types', name: 'api_company_types', methods: ['GET'])]
    public function apiCompanyTypes(CompanyTypeRepository $companyTypeRepository): JsonResponse
    {
        $companyTypes = $companyTypeRepository->findAll();

        $data = array_map(function ($companyType) {
            return [
                'id' => $companyType->getId(),
                'name' => $companyType->getName(),
            ];
        }, $companyTypes);

        return $this->json($data);
    }
}