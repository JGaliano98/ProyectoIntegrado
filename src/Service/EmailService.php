<?php
// src/Service/EmailService.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Pedido;

class EmailService
{
    private $mailer;
    private $router;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendOrderConfirmation($toEmail, $pdfPath, $userName)
    {
        // Crear un correo electrónico
        $email = (new Email())
            ->from('email@email.com')
            ->to($toEmail)
            ->subject('Confirmación de tu Pedido')
            ->text("Hola $userName,\n\nGracias por realizar tu pedido. Adjuntamos la factura en formato PDF.\n\nSaludos,\nHnos Galiano Herreros.")
            ->html("<p>Hola $userName,</p><p>Gracias por realizar tu pedido. Adjuntamos la factura en formato PDF.</p><p>Saludos,<br/>Hnos Galiano Herreros.</p>");

        // Adjuntar el PDF de la factura
        if (file_exists($pdfPath)) {
            $email->attachFromPath($pdfPath, 'factura.pdf', 'application/pdf');
        }

        // Enviar el correo
        $this->mailer->send($email);
    }

    public function sendOrderProcessed($toEmail, $userName, Pedido $pedido)
    {
        // Mensaje inicial
        $mensajeTexto = "Hola $userName,\n\nEl pedido ha sido procesado y enviado. En un plazo de 3-5 días laborables le llegará su pedido. Gracias por su compra.\n\nHnos Galiano Herreros.\n\n";
        $mensajeHtml = "<p>Hola $userName,</p><p>El pedido ha sido procesado y enviado. En un plazo de 3-5 días laborables le llegará su pedido.</p><p>Gracias por su compra.<br/>Hnos Galiano Herreros.</p><br/>";

        // Enlaces de reseña para cada producto
        $mensajeTexto .= "Enlaces para dejar una reseña de los productos:\n";
        $mensajeHtml .= "<p>Enlaces para dejar una reseña de los productos:</p><ul>";

        foreach ($pedido->getDetallePedidos() as $detalle) {
            $producto = $detalle->getProducto();
            $urlResena = $this->router->generate('crear_resena', ['id' => $producto->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            
            $mensajeTexto .= "- " . $producto->getNombre() . ": $urlResena\n";
            $mensajeHtml .= "<li><a href=\"$urlResena\">" . $producto->getNombre() . "</a></li>";
        }

        $mensajeHtml .= "</ul>";

        // Crear un correo electrónico con los enlaces de reseña
        $email = (new Email())
            ->from('email@email.com')
            ->to($toEmail)
            ->subject('Pedido Procesado y Enviado')
            ->text($mensajeTexto)
            ->html($mensajeHtml);

        // Enviar el correo
        $this->mailer->send($email);
    }

    public function sendAdminNotification($toEmail, $productName, $message)
    {
        // Crear un correo electrónico para los administradores
        $email = (new Email())
            ->from('email@email.com')
            ->to($toEmail)
            ->subject('Producto agotado: ' . $productName)
            ->text("Hola Administrador,\n\nEl producto \"$productName\" se ha agotado.\n\n$message\n\nSaludos,\nHnos Galiano Herreros.")
            ->html("<p>Hola Administrador,</p><p>El producto <strong>$productName</strong> se ha agotado.</p><p>$message</p><p>Saludos,<br/>Hnos Galiano Herreros.</p>");

        // Enviar el correo
        $this->mailer->send($email);
    }

    public function enviarCorreoContacto(array $data)
    {
        // Crear el correo electrónico de contacto
        $email = (new Email())
            ->from($data['email'])  // El correo del remitente
            ->to('t1s7lopezm@gmail.com')  // Correo destino
            ->subject('Nuevo mensaje de contacto')
            ->html("
                <p>Has recibido un nuevo mensaje de contacto de:</p>
                <p><strong>Nombre:</strong> {$data['nombre']} {$data['apellidos']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <p><strong>Teléfono:</strong> {$data['telefono']}</p>
                <p><strong>Mensaje:</strong></p>
                <p>{$data['mensaje']}</p>
            ");

        // Enviar el correo
        $this->mailer->send($email);
    }
}
