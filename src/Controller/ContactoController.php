<?php
// src/Controller/ContactoController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactoType;
use App\Service\EmailService;

class ContactoController extends AbstractController
{
    #[Route('/contacto', name: 'app_contacto', methods: ['POST'])]
    public function contacto(Request $request, EmailService $emailService): JsonResponse
    {
        $form = $this->createForm(ContactoType::class);
        $form->handleRequest($request);

        // Verificar si es una petición AJAX (fetch)
        if ($request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                
                // Llamar al servicio de correo para enviar el mensaje
                $emailService->enviarCorreoContacto($data);

                // Respuesta exitosa en formato JSON
                return new JsonResponse(['success' => true]);
            }

            // Si el formulario no es válido, devolver errores en JSON
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            return new JsonResponse(['success' => false, 'error' => implode(', ', $errors)]);
        }

        // Si no es una solicitud AJAX, devolver un error
        return new JsonResponse(['success' => false, 'error' => 'Solicitud no válida.'], 400);
    }
}