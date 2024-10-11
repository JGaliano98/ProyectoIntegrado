<?php

namespace App\Controller\Admin;

use App\Entity\Direccion;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DireccionCrudController extends AbstractCrudController
{
    private $security;
    private $authorizationChecker;

    // Inyectamos el servicio Security y AuthorizationChecker a través del constructor
    public function __construct(SecurityBundleSecurity $security, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->security = $security;
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getEntityFqcn(): string
    {
        return Direccion::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        // Obtenemos el usuario autenticado
        $user = $this->security->getUser();

        // Verificamos si el usuario autenticado es una instancia de User
        if (!$user) {
            throw new \Exception('No user is authenticated');
        }

        // Obtenemos el query builder por defecto
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Si el usuario tiene el rol ROLE_ADMIN, no aplicamos ningún filtro (ve todas las direcciones)
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        // Si el usuario tiene el rol ROLE_USER, filtramos para que solo vea sus propias direcciones
        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $qb->andWhere('entity.user = :user')
               ->setParameter('user', $user);
        }

        return $qb;
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
