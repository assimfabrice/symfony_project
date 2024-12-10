<?php

namespace App\Controller;

use App\Entity\FieldColor;
use App\Entity\FieldColorAssociate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('ffieldcolor', name: 'ff_fieldcolor_')]
class ColorHandleFrontController extends AbstractController
{
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
    #[Route('/api/field-colors/associate', name: 'api_field_colors_associate', methods: ['GET'])]
    public function getFieldColorsAssociate(EntityManagerInterface $em): JsonResponse
    {
        $fieldColorAssociate = $em->getRepository(FieldColorAssociate::class)->findAll();
        $fieldColorAssociate = array_shift($fieldColorAssociate);
        return new JsonResponse([
            'emptyColor' => $fieldColorAssociate->getEmptyColor(),
            'filledColor' => $fieldColorAssociate->getFilledColor(),
        ]);
    }
}