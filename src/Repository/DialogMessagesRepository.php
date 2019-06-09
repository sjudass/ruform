<?php

namespace App\Repository;

use App\Entity\DialogMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DialogMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method DialogMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method DialogMessages[]    findAll()
 * @method DialogMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DialogMessagesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DialogMessages::class);
    }

    // /**
    //  * @return DialogMessages[] Returns an array of DialogMessages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DialogMessages
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
