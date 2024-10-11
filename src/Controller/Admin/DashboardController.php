<?php

namespace App\Controller\Admin;

use App\Entity\Categoria;
use App\Entity\DetallePedido;
use App\Entity\Direccion;
use App\Entity\Imagen;
use App\Entity\MetodoPago;
use App\Entity\Pedido;
use App\Entity\Producto;
use App\Entity\Resena;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Redirecciona al listado de productos
        return $this->redirectToRoute('admin_producto');
    }

    #[Route('/admin/producto', name: 'admin_producto')]
    public function producto(): Response
    {
        // Renderiza la vista del panel de administración (puedes ajustar la plantilla según tus necesidades)
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administrador de Productos');
    }

    public function configureMenuItems(): iterable
    {
        // Enlace a la página principal
        yield MenuItem::linkToUrl('Página Principal', 'fas fa-home', 'http://127.0.0.1:8000');

        // Elementos visibles solo para ROLE_ADMIN
        if ($this->isGranted('ROLE_ADMIN')) {
            // Sección para gestionar productos
            yield MenuItem::section('Gestión de Productos');
            yield MenuItem::linkToCrud('Productos', 'fas fa-drumstick-bite', Producto::class);
            yield MenuItem::linkToCrud('Categorías', 'fas fa-tags', Categoria::class);
            yield MenuItem::linkToCrud('Imágenes', 'fa fa-image', Imagen::class);

            // Sección para gestión avanzada
            yield MenuItem::section('Gestión Avanzada');
            yield MenuItem::linkToCrud('Detalle Pedidos', 'fas fa-receipt', DetallePedido::class);
            yield MenuItem::linkToCrud('Métodos de Pago', 'fas fa-credit-card', MetodoPago::class);
        }

        // Elementos visibles tanto para ROLE_ADMIN como ROLE_USER
        // Sección de Pedidos y Usuarios
        yield MenuItem::section('Gestión de Pedidos y Usuarios');
        yield MenuItem::linkToCrud('Pedidos', 'fas fa-shopping-cart', Pedido::class);
        yield MenuItem::linkToCrud('Usuarios', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Direcciones', 'fas fa-map-marker-alt', Direccion::class);
        yield MenuItem::linkToCrud('Reseñas', 'fas fa-star', Resena::class);
    }

    public function configureActions(): Actions
    {
        // Configura las acciones disponibles para los productos
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
