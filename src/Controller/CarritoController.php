<?php
// src/Controller/CarritoController.php

namespace App\Controller;

use App\Entity\Direccion;
use App\Entity\MetodoPago;
use App\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;

class CarritoController extends AbstractController
{
    #[Route('/carrito', name: 'carrito')]
    public function index(SessionInterface $session): Response
    {
        // Obtener el carrito de la sesión, o inicializar un carrito vacío si no existe
        $carrito = $session->get('carrito', []);

        return $this->render('carrito/index.html.twig', [
            'carrito' => $carrito,
        ]);
    }

    #[Route('/carrito/add/{id}', name: 'carrito_add')]
    public function add(int $id, SessionInterface $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        // Buscar el producto por su ID
        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            throw $this->createNotFoundException('El producto no existe');
        }

        // Comprobar si hay suficiente stock para añadir el producto al carrito
        $cantidadEnCarrito = isset($carrito[$id]) ? $carrito[$id]['cantidad'] : 0;
        if ($producto->getStock() <= $cantidadEnCarrito) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => 'No hay suficiente stock disponible para este producto.'], 400);
            }
            $this->addFlash('error', 'No hay suficiente stock disponible para este producto.');
            return $this->redirectToRoute('productos');
        }

        // Verificar si el producto ya está en el carrito
        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            $carrito[$id] = [
                'producto' => $producto,
                'cantidad' => 1,
            ];
        }

        // Guardar el carrito actualizado en la sesión
        $session->set('carrito', $carrito);

        if ($request->isXmlHttpRequest()) {
            // Responder con JSON para la solicitud AJAX
            return new JsonResponse(['success' => true, 'cartCount' => array_sum(array_column($carrito, 'cantidad'))]);
        }

        // Redirigir a la página del carrito en caso de una solicitud normal
        return $this->redirectToRoute('carrito');
    }

    #[Route('/carrito/increment/{id}', name: 'carrito_increment', methods: ['POST'])]
    public function increment(int $id, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        // Buscar el producto por su ID
        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            return new JsonResponse(['error' => 'Producto no encontrado'], 404);
        }

        // Comprobar si hay suficiente stock para incrementar la cantidad
        $cantidadEnCarrito = $carrito[$id]['cantidad'];
        if ($producto->getStock() <= $cantidadEnCarrito) {
            return new JsonResponse(['error' => 'No hay suficiente stock disponible para este producto'], 400);
        }

        // Incrementar la cantidad en el carrito
        $carrito[$id]['cantidad']++;
        $session->set('carrito', $carrito);

        // Calcular el nuevo total del carrito y el precio del producto actualizado
        $totalProducto = ($producto->getPrecio() * $carrito[$id]['cantidad']) / 100;
        $totalCarrito = array_sum(array_map(function ($item) {
            return $item['producto']->getPrecio() * $item['cantidad'];
        }, $carrito)) / 100;

        // Responder con los nuevos valores en formato JSON
        return new JsonResponse([
            'success' => true,
            'cantidad' => $carrito[$id]['cantidad'],
            'totalProducto' => number_format($totalProducto, 2, '.', ','),
            'totalCarrito' => number_format($totalCarrito, 2, '.', ',')
        ]);
    }

    #[Route('/carrito/decrement/{id}', name: 'carrito_decrement', methods: ['POST'])]
    public function decrement(int $id, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        // Buscar el producto por su ID
        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            return new JsonResponse(['error' => 'Producto no encontrado'], 404);
        }

        // Verificar si el producto está en el carrito y disminuir la cantidad
        if (isset($carrito[$id]) && $carrito[$id]['cantidad'] > 1) {
            $carrito[$id]['cantidad']--;
        } elseif (isset($carrito[$id]) && $carrito[$id]['cantidad'] === 1) {
            unset($carrito[$id]); // Eliminar si la cantidad es 1 y se decrece
        } else {
            return new JsonResponse(['error' => 'Producto no encontrado en el carrito'], 400);
        }

        // Guardar el carrito actualizado en la sesión
        $session->set('carrito', $carrito);

        // Calcular el nuevo total del carrito y el precio del producto actualizado
        $totalProducto = isset($carrito[$id]) ? ($producto->getPrecio() * $carrito[$id]['cantidad']) / 100 : 0;
        $totalCarrito = array_sum(array_map(function ($item) {
            return $item['producto']->getPrecio() * $item['cantidad'];
        }, $carrito)) / 100;

        // Responder con los nuevos valores en formato JSON
        return new JsonResponse([
            'success' => true,
            'cantidad' => $carrito[$id]['cantidad'] ?? 0,
            'totalProducto' => number_format($totalProducto, 2, '.', ','),
            'totalCarrito' => number_format($totalCarrito, 2, '.', ','),
            'carritoVacio' => empty($carrito)
        ]);
    }

    #[Route('/carrito/remove/{id}', name: 'carrito_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        // Verificar si el producto está en el carrito y eliminarlo
        if (isset($carrito[$id])) {
            unset($carrito[$id]);
        }

        // Actualizar la sesión con el nuevo carrito
        $session->set('carrito', $carrito);

        return $this->redirectToRoute('carrito');
    }





    #[Route('/resumen-pedido', name: 'resumen_pedido')]
    public function resumenPedido(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Verificar si el usuario está autenticado
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }

        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        // Verificar si el carrito está vacío
        if (empty($carrito)) {
            $this->addFlash('error', 'Tu carrito está vacío.');
            return $this->redirectToRoute('carrito');
        }

        // Obtener las direcciones del usuario actual
        $usuario = $this->getUser();
        $direcciones = $entityManager->getRepository(Direccion::class)->findBy(['user' => $usuario]);

        // Obtener los métodos de pago disponibles del usuario actual
        $metodosPago = $entityManager->getRepository(MetodoPago::class)->findAll();

        return $this->render('pedido/resumen.html.twig', [
            'carrito' => $carrito,
            'direcciones' => $direcciones,
            'metodosPago' => $metodosPago,
        ]);
    }


}
