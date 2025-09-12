<?php

namespace App\Repository;

use App\Entity\Jma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Exception;

/**
 * @extends ServiceEntityRepository<Jma>
 */
class JmaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jma::class);
    }



    /**
     * @return Jma[] Returns an array of Jma objects
     */
    public function findJmaByEvent($evento): ?array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql ="select * From jma Where idEvento = '".$evento."'";
        try {
            $datos= $conn->executeQuery($sql);
            return $datos->fetchAllAssociative();
        }catch (Exception $e){
            return [];
        }
    }

    //    public function findOneBySomeField($value): ?Jma
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
