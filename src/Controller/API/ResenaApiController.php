<?php
// src/Controller/Api/ResenaApiController.php

namespace App\Controller\Api;

use App\Entity\Resena;
use App\Entity\Producto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResenaApiController extends AbstractController
{
    #[Route('/api/resena', name: 'api_crear_resena', methods: ['POST'])]
    public function crearResena(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['success' => false, 'message' => 'Debes iniciar sesión para escribir una reseña.'], 401);
        }

        // Obtener datos del request
        $data = json_decode($request->getContent(), true);

        // Validar datos
        if (empty($data['producto_id']) || empty($data['puntuacion']) || empty($data['comentario'])) {
            return new JsonResponse(['success' => false, 'message' => 'Datos incompletos.'], 400);
        }

        $producto = $entityManager->getRepository(Producto::class)->find($data['producto_id']);
        if (!$producto) {
            return new JsonResponse(['success' => false, 'message' => 'Producto no encontrado.'], 404);
        }

        // Crear nueva reseña
        $resena = new Resena();
        $resena->setPuntuacion($data['puntuacion']);
        $resena->setComentario($data['comentario']);
        $resena->setFecha(new \DateTime());
        $resena->setProducto($producto);
        $resena->setUser($user);

        // Guardar en la base de datos
        $entityManager->persist($resena);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Reseña guardada con éxito.']);
    }
}
