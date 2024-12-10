<?php

namespace App\Controller;
use App\Entity\FieldColorAssociate;
use App\Entity\User;
use App\Entity\FieldColor;
use App\Form\FieldColorType;
use App\Form\FieldColorTypeAssociateType;
use App\Repository\FieldColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/fieldcolor', name: 'app_fieldcolor_')]
class FieldColorController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FieldColorRepository $fieldColorRepository,
    )
    {
    }

    #[Route('/', name: 'index')]
    public function fieldColors(EntityManagerInterface $em, Request $request): Response
    {
        $fieldColors = $em->getRepository(FieldColor::class)->findAll();

        $forms = [];
        foreach ($fieldColors as $fieldColor) {

            $form = $this->createForm(FieldColorType::class, $fieldColor);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($fieldColor);
                $em->flush();
            }

            $forms[] = $form->createView();
        }

        return $this->render('field_color/field_colors.html.twig', [
            'forms' => $forms,
        ]);
    }
    #[Route('/update-field-color/{fieldName}', name: 'update_field_color', methods: ['POST'])]
    public function updateFieldColor(Request $request, int $fieldName): Response
    {
        // Récupérer les données du formulaire pour le champ en question
        $form = $this->createForm(FieldColorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Logique de sauvegarde pour ce champ précis
            $data = $form->getData();

            $this->saveFieldColor($fieldName, $data);

            return $this->json([
                'success' => 'Le champs ' . $data->getFieldName()
            ]);
        }

        return $this->redirectToRoute('app_fieldcolor_index');
    }

    private function saveFieldColor(string $fieldName, $data): void
    {

        $field = $this->entityManager->getRepository(FieldColor::class)->find($fieldName);
        $field->setEmptyColor($data->getEmptyColor());
        $field->setFilledColor($data->getFilledColor());

        $this->entityManager->persist($field);
        $this->entityManager->flush();
    }

    #[Route('/api/field-colors', name: 'api_field_colors', methods: ['GET'])]
    public function getFieldColors(EntityManagerInterface $em): JsonResponse
    {
        $fieldColors = $em->getRepository(FieldColor::class)->findAll();

        $colors = [];
        foreach ($fieldColors as $fieldColor) {
            $colors[$fieldColor->getFieldName()] = [
                'emptyColor' => $fieldColor->getEmptyColor(),
                'filledColor' => $fieldColor->getFilledColor(),
            ];
        }

        return new JsonResponse($colors);
    }
    #[Route('/associate', name: 'associate')]
    public function fieldColorsAssociate(EntityManagerInterface $em, Request $request): Response
    {
        $fieldColorAssociate = $em->getRepository(FieldColorAssociate::class)->findAll();
        $fieldColorAssociate = array_shift($fieldColorAssociate);

        $form = $this->createForm(FieldColorTypeAssociateType::class, $fieldColorAssociate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($fieldColorAssociate);
            $em->flush();
        }

        return $this->render('field_color/field_colors_associate.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/associate-update-field-color', name: 'associate_update_field_color', methods: ['POST'])]
    public function updateAssociateFieldColor(Request $request): Response
    {
        // Récupérer les données du formulaire pour le champ en question
        $form = $this->createForm(FieldColorTypeAssociateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Exemple : Sauvegarder dans une base de données ou un fichier de configuration
            $this->saveFieldColorAssociate($data);

            return $this->json([
                'success' => 'Les couleurs des champs ont été modifiés avec succès :)'
            ]);
        }

        return $this->redirectToRoute('app_fieldcolor_index');
    }
    private function saveFieldColorAssociate($data): void
    {
        $field = $this->entityManager->getRepository(FieldColorAssociate::class)->findAll()[0];

        $field->setEmptyColor($data->getEmptyColor());
        $field->setFilledColor($data->getFilledColor());

        $this->entityManager->persist($field);
        $this->entityManager->flush();

    }
}
