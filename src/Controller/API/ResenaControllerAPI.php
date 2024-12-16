<?php

namespace App\Controller\API;

use App\Entity\Resena;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResenaControllerAPI extends AbstractController
{
    #[Route('/api/resena/{id}/like', name: 'api_resena_like', methods: ['POST'])]
    public function like(Resena $resena, EntityManagerInterface $entityManager): JsonResponse
    {
        $resena->incrementLikes();
        $entityManager->flush();

        return new JsonResponse(['likes' => $resena->getLikes()]);
    }

    #[Route('/api/resena/{id}/dislike', name: 'api_resena_dislike', methods: ['POST'])]
    public function dislike(Resena $resena, EntityManagerInterface $entityManager): JsonResponse
    {
        $resena->incrementDislikes();
        $entityManager->flush();

        return new JsonResponse(['dislikes' => $resena->getDislikes()]);
    }
}