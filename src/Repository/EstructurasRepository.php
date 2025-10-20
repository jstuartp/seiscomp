<?php

namespace App\Repository;

use App\Entity\Estructuras;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Exception;

/**
 * @extends ServiceEntityRepository<Estructuras>
 */
class EstructurasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estructuras::class);
    }


    /**
     * Obtener Listado de los Archivos LIS creados
     */
    public function findAllEstructuras(): ?array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql ="select * From estructuras";
        try {
            $datos= $conn->executeQuery($sql);
            return $datos->fetchAllAssociative();
        }catch (Exception $e){
            return [];
        }


    }



    //    /**
    //     * @return Estructuras[] Returns an array of Estructuras objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Estructuras
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
