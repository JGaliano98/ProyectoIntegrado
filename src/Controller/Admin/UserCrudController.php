<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;
    private $security;
    private $authorizationChecker;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SecurityBundleSecurity $security, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->passwordHasher = $passwordHasher;
        $this->security = $security;
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, \EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection $filters): ORMQueryBuilder
    {
        // Obtener el usuario autenticado
        $user = $this->security->getUser();

        // Verificar si el usuario autenticado es una instancia de la entidad User
        if (!$user instanceof User) {
            throw new \Exception('El usuario autenticado no es de la clase User.');
        }

        // Obtener el query builder por defecto
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Si el usuario tiene el rol ROLE_ADMIN, puede ver todos los registros
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        // Si el usuario no es administrador, filtramos para que solo vea sus propios registros
        $qb->andWhere('entity.id = :id')
           ->setParameter('id', $user->getId());

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('email', 'Correo electrónico'),
            TextField::new('nombre', 'Nombre'),
            TextField::new('apellido1', 'Primer Apellido'),
            TextField::new('apellido2', 'Segundo Apellido'),
            IntegerField::new('telefono', 'Teléfono'),
        ];

        if ($pageName === Crud::PAGE_NEW) {
            $fields[] = TextField::new('plainPassword', 'Contraseña')
                ->setFormType(PasswordType::class)
                ->setRequired(true);
        }

        if ($pageName === Crud::PAGE_EDIT) {
            $fields[] = TextField::new('plainPassword', 'Nueva Contraseña')
                ->setFormType(PasswordType::class)
                ->setRequired(false);
        }

        return $fields;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        if ($entityInstance->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPlainPassword());
            $entityInstance->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        // Solo hashea y actualiza la contraseña si se proporciona una nueva
        if ($entityInstance->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPlainPassword());
            $entityInstance->setPassword($hashedPassword);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}