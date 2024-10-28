<?php

namespace App\Repository;

use App\Entity\DetallePedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class DetallePedidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetallePedido::class);
    }

   
    // public function findByUserAndProduct($user, $producto): array
    // {
    //     return $this->createQueryBuilder('d')
    //         ->addSelect('p', 'u')                 // Incluimos 'Pedido' y 'User' en la consulta
    //         ->innerJoin('d.pedido', 'p')          // Hacemos un join entre DetallePedido y Pedido
    //         ->innerJoin('p.user', 'u')            // Hacemos un join entre Pedido y User
    //         ->andWhere('u = :user')               // Filtramos por el usuario
    //         ->andWhere('d.producto = :producto')  // Filtramos por el producto
    //         ->setParameter('user', $user)
    //         ->setParameter('producto', $producto)
    //         ->getQuery()
    //         ->getResult();
    // }

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
    


}
