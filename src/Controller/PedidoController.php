<?php
// src/Controller/PedidoController.php

namespace App\Controller;

use App\Entity\DetallePedido;
use App\Entity\Direccion;
use App\Entity\Pedido;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\EmailService;
use Dompdf\Dompdf;
use Dompdf\Options;

class PedidoController extends AbstractController
{
    #[Route('/realizar-pedido', name: 'realizar_pedido', methods: ['POST'])]
    public function realizarPedido(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'Tu carrito está vacío.');
            return $this->redirectToRoute('carrito');
        }

        // Obtener los datos del formulario de resumen de pedido
        $direccionId = $request->request->get('direccion');
        $direccion = $entityManager->getRepository(Direccion::class)->find($direccionId);

        if (!$direccion) {
            $this->addFlash('error', 'Por favor, selecciona una dirección válida.');
            return $this->redirectToRoute('resumen_pedido');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \Exception("El usuario no está autenticado o no es una instancia válida.");
        }

        // Crear el pedido
        $pedido = new Pedido();
        $pedido->setFecha(new \DateTime());
        $pedido->setEstado('pendiente');
        $pedido->setNombreUsuario($user->getNombre());
        $pedido->setEmailUsuario($user->getEmail());
        $pedido->setCalleDireccion($direccion->getCalle());
        $pedido->setNumeroDireccion($direccion->getNumero());
        $pedido->setLocalidadDireccion($direccion->getLocalidad());
        $pedido->setProvinciaDireccion($direccion->getProvincia());
        $pedido->setCodigoPostalDireccion($direccion->getCodigoPostal());
        $pedido->setPaisDireccion($direccion->getPais());
        $pedido->setOtraInfoDireccion($direccion->getOtros());
        $pedido->setUser($user);

        $entityManager->persist($pedido);
        $totalCarrito = 0;

        // Crear detalles del pedido
        foreach ($carrito as $item) {
            // Obtener el producto desde la base de datos
            $producto = $entityManager->getRepository($item['producto']::class)->find($item['producto']->getId());
            $cantidad = $item['cantidad'];
            $precioTotalProducto = ($producto->getPrecio() * $cantidad) / 100;

            if (!$producto) {
                $this->addFlash('error', 'Error con el producto seleccionado.');
                return $this->redirectToRoute('carrito');
            }

            $detallePedido = new DetallePedido();
            $detallePedido->setCantidad($cantidad);
            $detallePedido->setPrecio($precioTotalProducto);
            $detallePedido->setNombreProducto($producto->getNombre());
            $detallePedido->setDescripcionProducto($producto->getDescripcion());
            $detallePedido->setPrecioProducto($producto->getPrecio());
            $detallePedido->setProducto($producto);
            $detallePedido->setPedido($pedido);

            $entityManager->persist($detallePedido);

            $totalCarrito += $precioTotalProducto;
        }

        // Actualizar el total del pedido
        $pedido->setTotal($totalCarrito);

        // Guardar el pedido y los detalles en la base de datos
        $entityManager->flush();

        // Generar el PDF del pedido
        $html = $this->renderView('pedido/factura.html.twig', [
            'carrito' => $carrito,
            'direccion' => $direccion,
            'user' => $user,
        ]);

        try {
            // Configurar DOMPDF
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Guardar el PDF temporalmente
            $pdfOutput = $dompdf->output();
            $pdfFilePath = __DIR__ . '/../../public/factura_generada.pdf';
            file_put_contents($pdfFilePath, $pdfOutput);

            // Enviar el correo de confirmación con el PDF adjunto
            $emailService->sendOrderConfirmation($user->getEmail(), $pdfFilePath, $user->getNombre());

            // Limpiar el carrito después de realizar el pedido y generar el PDF
            $session->remove('carrito');

            $this->addFlash('success', 'Tu pedido ha sido realizado con éxito.');

            // Redirigir a la página de confirmación del pedido
            return $this->redirectToRoute('pedido_confirmacion');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Error generando el PDF: ' . $e->getMessage());
            return $this->redirectToRoute('carrito');
        }
    }

    #[Route('/confirmacion-pedido', name: 'pedido_confirmacion')]
    public function confirmacionPedido(): Response
    {
        return $this->render('pedido/confirmacion.html.twig', [
            'pdfPath' => '/factura_generada.pdf'
        ]);
    }

    #[Route('/descargar-pdf', name: 'descargar_pdf')]
    public function descargarPdf(): Response
    {
        $pdfFilePath = __DIR__ . '/../../public/factura_generada.pdf';

        if (!file_exists($pdfFilePath)) {
            $this->addFlash('error', 'No se pudo encontrar la factura generada.');
            return $this->redirectToRoute('pedido_confirmacion');
        }

        // Descargar el PDF automáticamente para el usuario
        return new Response(file_get_contents($pdfFilePath), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="factura.pdf"'
        ]);
    }
}