<?php

namespace App\Controller\Admin;

use App\Entity\Direccion;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DireccionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Direccion::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Direccion) {
            return;
        }

        // Obtener el usuario actual desde el servicio de seguridad
        $usuario = $this->getUser(); // Método heredado de AbstractController
        if ($usuario) {
            $entityInstance->setUser($usuario);
        }

        // Persistir la entidad
        parent::persistEntity($entityManager, $entityInstance);
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
