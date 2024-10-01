<?php
// src/Controller/ProductoController.php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Categoria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductoController extends AbstractController
{
    #[Route('/productos', name: 'productos')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Obtener el parámetro de ordenación, por defecto es 'nombre'
        $ordenarPor = $request->query->get('orden', 'nombre');
        $orden = $request->query->get('direccion', 'ASC'); // ASC o DESC para dirección del orden

        // Si el filtro es 'categoria', obtenemos las categorías con sus productos
        if ($ordenarPor === 'categoria') {
            $categorias = $entityManager->getRepository(Categoria::class)->findAll();

            return $this->render('productos/index.html.twig', [
                'categorias' => $categorias,
                'ordenarPor' => $ordenarPor,
            ]);
        } else {
            // Si no es por categoría, aplicamos el filtro y dirección
            $productos = $entityManager->getRepository(Producto::class)->findBy([], [$ordenarPor => $orden]);

            return $this->render('productos/index.html.twig', [
                'productos' => $productos,
                'ordenarPor' => $ordenarPor,
                'orden' => $orden,
            ]);
        }
    }
}
