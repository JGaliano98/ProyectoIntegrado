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

        // Sección para gestionar productos
        yield MenuItem::section('Gestión');
        yield MenuItem::linkToCrud('Productos', 'fas fa-drumstick-bite', Producto::class);
        yield MenuItem::linkToCrud('Categorias', 'fas fa-tags', Categoria::class);
        yield MenuItem::linkToCrud('Detalle Pedidos', 'fas fa-receipt', DetallePedido::class);
        yield MenuItem::linkToCrud('Direcciones', 'fas fa-map-marker-alt', Direccion::class);
        yield MenuItem::linkToCrud('Metodos de Pago', 'fas fa-credit-card', MetodoPago::class);
        yield MenuItem::linkToCrud('Pedidos', 'fas fa-shopping-cart', Pedido::class);
        yield MenuItem::linkToCrud('Reseñas', 'fas fa-star', Resena::class);
        yield MenuItem::linkToCrud('Usuarios', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Imágenes', 'fa fa-image', Imagen::class);
        
    }

    public function configureActions(): Actions
    {
        // Configura las acciones disponibles para los productos
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
