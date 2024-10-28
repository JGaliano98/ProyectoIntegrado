<?php

namespace App\Repository;

use App\Entity\Resena;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resena>
 */
class ResenaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resena::class);
    }

    public function findOneByUserAndProduct($user, $producto): ?Resena
{
    return $this->createQueryBuilder('r')
        ->andWhere('r.user = :user')
        ->andWhere('r.producto = :producto')
        ->setParameter('user', $user)
        ->setParameter('producto', $producto)
        ->getQuery()
        ->getOneOrNullResult();
}

public function findByUserAndProduct($user, $producto): array
{
    return $this->createQueryBuilder('d')
        ->join('d.pedido', 'p')
        ->andWhere('p.user = :user')
        ->andWhere('d.producto = :producto')
        ->setParameter('user', $user)
        ->setParameter('producto', $producto)
        ->getQuery()
        ->getResult();
}


public function findOneByUserProductAndPedido($user, $producto, $pedido): ?Resena
{
    return $this->createQueryBuilder('r')
        ->join('r.producto', 'p')
        ->join('p.detallePedidos', 'dp')
        ->andWhere('r.user = :user')
        ->andWhere('r.producto = :producto')
        ->andWhere('dp.pedido = :pedido')
        ->setParameter('user', $user)
        ->setParameter('producto', $producto)
        ->setParameter('pedido', $pedido)
        ->getQuery()
        ->getOneOrNullResult();
}

    //    /**
    //     * @return Resena[] Returns an array of Resena objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Resena
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
