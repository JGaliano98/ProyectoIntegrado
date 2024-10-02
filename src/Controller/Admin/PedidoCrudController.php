<?php

namespace App\Controller\Admin;

use App\Entity\Pedido;
use App\Entity\DetallePedido;
use App\Entity\User;
use App\Service\EmailService;
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


#[Route('/pedido/{id}/procesar', name: 'admin_pedido_procesar')]
public function procesarPedido(int $id, EntityManagerInterface $entityManager, EmailService $emailService): Response
{
    // Obtener el pedido por su ID
    $pedido = $entityManager->getRepository(Pedido::class)->find($id);

    if (!$pedido) {
        $this->addFlash('error', 'El pedido no existe.');
        return $this->redirectToRoute('admin');
    }

    // Cambiar el estado del pedido a 'procesado'
    $pedido->setEstado('procesado');
    $entityManager->flush();

    // Enviar el correo de confirmación de envío al usuario
    $user = $pedido->getUser();
    if ($user instanceof User) {
        $emailService->sendOrderProcessed($user->getEmail(), $user->getNombre());
    }

    $this->addFlash('success', 'El pedido ha sido procesado correctamente y el usuario ha sido notificado.');
    return $this->redirectToRoute('admin', [
        'crudAction' => 'index',
        'crudControllerFqcn' => PedidoCrudController::class,
    ]);
}

}