<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactoType;
use App\Service\EmailService;
use Symfony\Component\Finder\Finder;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images/carrusel';
        $finder = new Finder();
        $finder->files()->in($imageDirectory)->name('/\.(jpg|jpeg|png|gif)$/i');

        $images = [];
        foreach ($finder as $file) {
            $images[] = '/images/carrusel/' . $file->getFilename();
        }

        return $this->render('home/index.html.twig', [
            'images' => $images
        ]);
    }

    #[Route('/conocenos', name: 'conocenos')]
    public function conocenos(): Response
    {
        return $this->render('conocenos/conocenos.html.twig');
    }

    #[Route('/contactanos', name: 'contactanos')]
    public function contactanos(Request $request, EmailService $emailService): Response
    {
        // Crear el formulario de contacto
        $form = $this->createForm(ContactoType::class);
        $form->handleRequest($request);

        // Si el formulario es enviado y válido, enviar el correo
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $emailService->enviarCorreoContacto($data);

            $this->addFlash('success', 'Tu mensaje ha sido enviado correctamente.');

            return $this->redirectToRoute('contactanos');
        }

        // Renderizar la vista con el formulario
        return $this->render('contactanos/contactanos.html.twig', [
            'form' => $form->createView(),  // Pasar el formulario a la vista
        ]);
    }
}
