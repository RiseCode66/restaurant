<?php

namespace App\Repository;
use App\Entity\Meme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Client;
/**
 * @extends ServiceEntityRepository<Meme>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

//    /**
//     * @return Meme[] Returns an array of Meme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

public function findOneByEmail($value): ?Client
{
    return $this->createQueryBuilder('u')
        ->andWhere('u.email = :email')
        ->setParameter('email', $value)
        ->getQuery()
        ->getOneOrNullResult();
}
}
