<?php

namespace App\Controller\Admin;

use App\Entity\Pedido;
use App\Entity\DetallePedido;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class PedidoCrudController extends AbstractCrudController
{
    private $adminUrlGenerator;
    private $entityManager;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Pedido::class;
    }

    

    public function configureFields(string $pageName): iterable
{
    $fields = [
        IdField::new('id')->onlyOnIndex(),
        DateField::new('fecha')->setFormat('dd-MM-yyyy HH:mm'), // Formato "día-mes-año horas:minutos"
        TextField::new('estado', 'Estado'),
        TextField::new('nombreCompletoUsuario', 'Nombre Completo')->onlyOnDetail(),
        TextField::new('email_usuario', 'Email Cliente'),
        TextField::new('direccionCompleta', 'Dirección')->onlyOnDetail(),
    ];




    if ($pageName === Crud::PAGE_DETAIL) {
        // Agregar los detalles del pedido en la vista detallada
        $fields[] = CollectionField::new('detallePedidos', 'Detalles del Pedido')
            ->setTemplatePath('admin/detalle_pedido.html.twig');
    }

    return $fields;
}

    

    

    public function configureActions(Actions $actions): Actions
{
    $procesar = Action::new('procesar', 'Procesar Pedido')
        ->linkToRoute('admin_pedido_procesar', function (Pedido $pedido): array {
            return ['id' => $pedido->getId()];
        })
        ->setCssClass('btn btn-success')
        ->displayIf(static function (Pedido $pedido) {
            return $pedido->getEstado() === 'pendiente';
        });

    return $actions
        ->add(Crud::PAGE_INDEX, $procesar)
        ->add(Crud::PAGE_DETAIL, $procesar);
}


    #[Route('/admin/pedido/procesar/{id}', name: 'admin_pedido_procesar', methods: ['GET', 'POST'])]
    public function procesarPedido(int $id): Response
    {
        $pedido = $this->entityManager->getRepository(Pedido::class)->find($id);

        if (!$pedido) {
            $this->addFlash('error', 'Pedido no encontrado.');
            return $this->redirect($this->adminUrlGenerator->setController(self::class)->setAction(Crud::PAGE_INDEX)->generateUrl());
        }

        $pedido->setEstado('procesado');
        $this->entityManager->flush();

        $this->addFlash('success', 'El pedido ha sido procesado con éxito.');

        return $this->redirect($this->adminUrlGenerator->setController(self::class)->setAction(Crud::PAGE_INDEX)->generateUrl());
    }
}
