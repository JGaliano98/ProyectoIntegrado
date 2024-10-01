<?php
// src/Service/EmailService.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mime\Part\Multipart\MixedPart;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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
}
