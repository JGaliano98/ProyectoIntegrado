<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Define la ruta de la carpeta donde se encuentran las imágenes
        $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images/carrusel';

        // Utiliza Finder para buscar todas las imágenes en la carpeta
        $finder = new Finder();
        $finder->files()->in($imageDirectory)->name('/\.(jpg|jpeg|png|gif)$/i');

        // Crea un array para almacenar las rutas de las imágenes
        $images = [];
        foreach ($finder as $file) {
            $images[] = '/images/carrusel/' . $file->getFilename();
        }

        // Renderiza la plantilla con la lista de imágenes
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
    public function contactanos(): Response
    {
        return $this->render('contactanos/contactanos.html.twig');
    }
}
