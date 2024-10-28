<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Resena;
use App\Repository\DetallePedidoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResenaController extends AbstractController
{
    private $detallePedidoRepository;
    private $entityManager;

    public function __construct(DetallePedidoRepository $detallePedidoRepository, EntityManagerInterface $entityManager)
    {
        $this->detallePedidoRepository = $detallePedidoRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/producto/{id}/resena', name: 'crear_resena', methods: ['POST', 'GET'])]
    public function crearResena(Request $request, Producto $producto): Response
    {
        $user = $this->getUser();

        // Verificar si el usuario ha iniciado sesión
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Verificar si el usuario ha comprado el producto
        $detalleCompra = $this->detallePedidoRepository->findByUserAndProduct($user, $producto);

        if (!$detalleCompra) {
            $this->addFlash('error', 'No puedes reseñar este producto ya que no lo has comprado.');
            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            // Crear y guardar la reseña
            $resena = new Resena();
            $resena->setProducto($producto);
            $resena->setUser($user);
            $resena->setPuntuacion($request->request->get('puntuacion'));
            $resena->setComentario($request->request->get('comentario'));
            $resena->setFecha(new \DateTime());

            $this->entityManager->persist($resena);
            $this->entityManager->flush();

            $this->addFlash('success', 'Reseña creada correctamente.');
            return $this->redirectToRoute('home'); 
        }

        return $this->render('resena/crear_resena.html.twig', [
            'producto' => $producto,
        ]);
    }
}
