<?php
// src/Controller/PedidoController.php

namespace App\Controller;

use App\Entity\DetallePedido;
use App\Entity\Direccion;
use App\Entity\Pedido;
use App\Entity\Producto;
use App\Entity\CompaniaTransporte;
use App\Entity\MetodoPago;
use App\Entity\User;
use App\Repository\CompaniaTransporteRepository;
use App\Repository\DireccionRepository;
use App\Repository\MetodoPagoRepository;
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
    #[Route('/resumen-pedido', name: 'resumen_pedido')]
    public function resumenPedido(
        SessionInterface $session,
        CompaniaTransporteRepository $companiaTransporteRepository,
        DireccionRepository $direccionRepository,
        MetodoPagoRepository $metodoPagoRepository
    ): Response {
        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        // Suponiendo que tienes un usuario autenticado
        $user = $this->getUser();

        // Obtener direcciones del usuario
        $direcciones = $direccionRepository->findBy(['user' => $user]);

        // Obtener todos los métodos de pago disponibles
        $metodosPago = $metodoPagoRepository->findAll();

        // Obtener todas las compañías de transporte
        $companiasTransporte = $companiaTransporteRepository->findAll();

        return $this->render('pedido/resumen.html.twig', [
            'carrito' => $carrito,
            'direcciones' => $direcciones,
            'metodosPago' => $metodosPago,
            'companiasTransporte' => $companiasTransporte,
        ]);
    }

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

        // Obtener la compañía de transporte seleccionada
        $companiaTransporteId = $request->request->get('compania_transporte');
        $companiaTransporte = $entityManager->getRepository(CompaniaTransporte::class)->find($companiaTransporteId);

        if (!$companiaTransporte) {
            $this->addFlash('error', 'Por favor, selecciona una compañía de transporte válida.');
            return $this->redirectToRoute('resumen_pedido');
        }

        // Obtener el método de pago seleccionado
        $metodoPagoNombre = $request->request->get('metodo_pago_nombre');

        if (!$metodoPagoNombre) {
            $this->addFlash('error', 'Por favor, selecciona un método de pago válido.');
            return $this->redirectToRoute('resumen_pedido');
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
        $pedido->setCompaniaTransporte($companiaTransporte);
        $pedido->setMetodoPago($metodoPagoNombre);

        // Generar un código de envío aleatorio
        $codigoEnvio = 'ENV' . strtoupper(bin2hex(random_bytes(5)));
        $pedido->setCodigoEnvio($codigoEnvio);

        $entityManager->persist($pedido);
        $totalCarrito = 0;
        $productosAgotados = [];

        // Crear detalles del pedido y actualizar el stock
        foreach ($carrito as $item) {
            $producto = $entityManager->getRepository(Producto::class)->find($item['producto']->getId());
            $cantidad = $item['cantidad'];
            $precioTotalProducto = ($producto->getPrecio() * $cantidad) / 100;

            if (!$producto) {
                $this->addFlash('error', 'Error con el producto seleccionado.');
                return $this->redirectToRoute('carrito');
            }

            if ($producto->getStock() < $cantidad) {
                $this->addFlash('error', 'El producto ' . $producto->getNombre() . ' no tiene suficiente stock.');
                return $this->redirectToRoute('carrito');
            }

            $nuevoStock = $producto->getStock() - $cantidad;
            $producto->setStock($nuevoStock);

            if ($nuevoStock <= $producto->getStockMin()) {
                $productosAgotados[] = $producto;
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

        $pedido->setTotal($totalCarrito);
        $entityManager->flush();

        if (!empty($productosAgotados)) {
            $admins = $entityManager->getRepository(User::class)->findByRole('ROLE_ADMIN');
            foreach ($admins as $admin) {
                foreach ($productosAgotados as $productoAgotado) {
                    $mensaje = 'El producto "' . $productoAgotado->getNombre() . '" se ha agotado. Por favor, repón el stock lo antes posible.';
                    $emailService->sendAdminNotification($admin->getEmail(), $productoAgotado->getNombre(), $mensaje);
                }
            }
        }

        $html = $this->renderView('pedido/factura.html.twig', [
            'carrito' => $carrito,
            'direccion' => $direccion,
            'user' => $user,
            'pedido' => $pedido,
            'companiaTransporte' => $companiaTransporte,
        ]);

        try {
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdfOutput = $dompdf->output();
            $pdfFilePath = __DIR__ . '/../../public/factura_generada.pdf';
            file_put_contents($pdfFilePath, $pdfOutput);

            $emailService->sendOrderConfirmation($user->getEmail(), $pdfFilePath, $user->getNombre(), $pedido);

            $session->remove('carrito');

            $this->addFlash('success', 'Tu pedido ha sido realizado con éxito.');
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


    #[Route('/seguimiento-pedido/{codigoEnvio}', name: 'pedido_seguimiento')]
    public function seguimientoPedido($codigoEnvio, EntityManagerInterface $entityManager): Response
    {
        // Buscar el pedido por el código de envío
        $pedido = $entityManager->getRepository(Pedido::class)->findOneBy(['codigoEnvio' => $codigoEnvio]);

        if (!$pedido) {
            throw $this->createNotFoundException('El pedido no fue encontrado.');
        }

        // Renderizar la vista de seguimiento
        return $this->render('pedido/seguimiento.html.twig', [
            'pedido' => $pedido
        ]);
    }
}