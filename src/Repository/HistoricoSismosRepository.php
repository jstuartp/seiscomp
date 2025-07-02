<?php

namespace App\Repository;

use App\Entity\HistoricoSismos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Exception;

/**
 * @extends ServiceEntityRepository<HistoricoSismos>
 */
class HistoricoSismosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoricoSismos::class);
    }


    /**
     * Obtener Todos los sismos registrados en la tabla historico_sismos
     */
    public function findHistoricoSismos(): ?array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql ="select * from historico_sismos
                Order by fechaEvento DESC;";
        try {

            $datos= $conn->executeQuery($sql);
            return $datos->fetchAllAssociative();
        }catch (Exception $e){
            return [];
        }


    }


    //    /**
    //     * @return HistoricoSismos[] Returns an array of HistoricoSismos objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HistoricoSismos
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
