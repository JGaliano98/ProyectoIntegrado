<?php

// src/Controller/PedidoController.php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\EmailService;
use Dompdf\Dompdf;
use Dompdf\Options;

class PedidoController extends AbstractController
{
    #[Route('/realizar-pedido', name: 'realizar_pedido')]
    public function realizarPedido(SessionInterface $session, EmailService $emailService): Response
    {
        // Obtener el carrito de la sesión
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'Tu carrito está vacío.');
            return $this->redirectToRoute('carrito');
        }

        // Generar el HTML del PDF basado en los datos del carrito
        $html = $this->renderView('pedido/factura.html.twig', [
            'carrito' => $carrito,
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

            // Verificar si el archivo se ha creado correctamente
            if (!file_exists($pdfFilePath)) {
                throw new \Exception('No se pudo generar el PDF.');
            }

            // Enviar el correo de confirmación con el PDF adjunto
            $user = $this->getUser();

            if ($user instanceof User) {
                $emailService->sendOrderConfirmation($user->getEmail(), $pdfFilePath, $user->getNombre());
            } else {
                $this->addFlash('error', 'No se pudo enviar el correo de confirmación. Usuario no autenticado.');
            }

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
