<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Societe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('admin/societe', name: 'app_societe_')]
class SocieteController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $societes = $this->entityManager->getRepository(Societe::class)->findSocieteNotNull();
        return $this->render('societes/index.html.twig', [
            'societes' => $societes,
        ]);
    }
    #[Route('/{id}/associates', name: 'associates')]
    public function getAssociates(Societe $societe)
    {
        $associates = $societe->getAssociates(); // Relation avec les associés
        return $this->render('societes/associates.html.twig', [
            'associates' => $associates,
        ]);
    }
}
