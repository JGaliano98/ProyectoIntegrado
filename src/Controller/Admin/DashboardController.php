<?php

namespace App\Controller\Admin;

// Importación de entidades, configuraciones y clases necesarias para EasyAdmin
use App\Entity\Categoria;
use App\Entity\CompaniaTransporte;
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

// Controlador principal para el panel de administración
class DashboardController extends AbstractDashboardController
{
    // Ruta principal del panel de administración
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Renderiza una vista personalizada para el panel (puedes personalizar el contenido del iframe en el archivo Twig)
        return $this->render('admin/dashboard_iframe.html.twig');
    }

    // Ruta específica para gestionar productos (puedes ajustar la vista según tus necesidades)
    #[Route('/admin/producto', name: 'admin_producto')]
    public function producto(): Response
    {
        // Renderiza la vista principal para la gestión de productos
        return $this->render('admin/index.html.twig');
    }

    // Configuración del título del panel de administración
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Carnicería - Charcutería Hnos Galiano Herreros'); // Título que aparece en el panel
    }

    // Configuración de los elementos del menú de navegación en el panel de administración
    public function configureMenuItems(): iterable
    {
        // Enlace a la página principal del sitio web
        yield MenuItem::linkToUrl('Página Principal', 'fas fa-home', 'http://127.0.0.1:8000');

        // Opciones visibles solo para usuarios con ROLE_ADMIN
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Gestión de Productos'); // Sección para productos
            yield MenuItem::linkToCrud('Productos', 'fas fa-drumstick-bite', Producto::class); // Gestión de productos
            yield MenuItem::linkToCrud('Categorías', 'fas fa-tags', Categoria::class); // Gestión de categorías
            yield MenuItem::linkToCrud('Imágenes', 'fa fa-image', Imagen::class); // Gestión de imágenes

            yield MenuItem::section('Gestión Avanzada'); // Sección avanzada
            yield MenuItem::linkToCrud('Detalle Pedidos', 'fas fa-receipt', DetallePedido::class); // Gestión de detalles de pedidos
            yield MenuItem::linkToCrud('Métodos de Pago', 'fas fa-credit-card', MetodoPago::class); // Gestión de métodos de pago
            yield MenuItem::linkToCrud('Compañías de Transporte', 'fas fa-truck', CompaniaTransporte::class); // Gestión de compañías de transporte

            yield MenuItem::section('Gestión de Pedidos y Reseñas'); // Sección para pedidos y reseñas
            yield MenuItem::linkToCrud('Pedidos', 'fas fa-shopping-cart', Pedido::class); // Gestión de pedidos
            yield MenuItem::linkToCrud('Reseñas', 'fas fa-star', Resena::class); // Gestión de reseñas

            yield MenuItem::section('Gestión de Usuarios'); // Sección para usuarios
            yield MenuItem::linkToCrud('Usuarios', 'fas fa-user', User::class); // Gestión de usuarios
            yield MenuItem::linkToCrud('Direcciones', 'fas fa-map-marker-alt', Direccion::class); // Gestión de direcciones
        }

        // Opciones visibles para usuarios con ROLE_MANAGER pero sin ROLE_ADMIN
        if ($this->isGranted('ROLE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Gestión de Productos'); // Igual que para ROLE_ADMIN
            yield MenuItem::linkToCrud('Productos', 'fas fa-drumstick-bite', Producto::class);
            yield MenuItem::linkToCrud('Categorías', 'fas fa-tags', Categoria::class);
            yield MenuItem::linkToCrud('Imágenes', 'fa fa-image', Imagen::class);

            yield MenuItem::section('Gestión Avanzada'); // Igual que para ROLE_ADMIN
            yield MenuItem::linkToCrud('Detalle Pedidos', 'fas fa-receipt', DetallePedido::class);
            yield MenuItem::linkToCrud('Métodos de Pago', 'fas fa-credit-card', MetodoPago::class);
            yield MenuItem::linkToCrud('Compañías de Transporte', 'fas fa-truck', CompaniaTransporte::class);

            yield MenuItem::section('Gestión de Pedidos y Reseñas'); // Igual que para ROLE_ADMIN
            yield MenuItem::linkToCrud('Pedidos', 'fas fa-shopping-cart', Pedido::class);
            yield MenuItem::linkToCrud('Reseñas', 'fas fa-star', Resena::class);
        }

        // Opciones visibles para usuarios con ROLE_USER, excluyendo managers y administradores
        if ($this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Gestión de Usuarios'); // Sección para usuarios y direcciones
            yield MenuItem::linkToCrud('Usuarios', 'fas fa-user', User::class);
            yield MenuItem::linkToCrud('Direcciones', 'fas fa-map-marker-alt', Direccion::class);

            yield MenuItem::section('Gestión de Pedidos y Reseñas'); // Sección para pedidos y reseñas
            yield MenuItem::linkToCrud('Pedidos', 'fas fa-shopping-cart', Pedido::class);
            yield MenuItem::linkToCrud('Reseñas', 'fas fa-star', Resena::class);
        }
    }

    // Configuración de acciones en EasyAdmin
    public function configureActions(): Actions
    {
        // Añadir acción para mostrar el detalle de las entidades en la vista de índice
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
