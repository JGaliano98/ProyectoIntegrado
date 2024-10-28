<?php

namespace App\Controller\Admin;

use App\Entity\Resena;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ResenaCrudController extends AbstractCrudController
{
    private $security;
    private $authorizationChecker;

    public function __construct(SecurityBundleSecurity $security, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->security = $security;
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getEntityFqcn(): string
    {
        return Resena::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new \Exception('No user is authenticated');
        }

        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $qb->andWhere('entity.user = :user')
               ->setParameter('user', $user);
        }

        return $qb;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Resena) {
            return;
        }

        $usuario = $this->getUser();
        if ($usuario) {
            $entityInstance->setUser($usuario);
        }

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
