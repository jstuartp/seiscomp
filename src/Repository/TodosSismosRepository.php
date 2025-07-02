<?php

namespace App\Repository;

use App\Entity\TodosSismos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Exception;

/**
 * @extends ServiceEntityRepository<TodosSismos>
 */
class TodosSismosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodosSismos::class);
    }


    /**
     * Obtener Todos los sismos registrados en la tabla todos_sismos
     */
    public function findTodosSismos(): ?array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql ="select * from todos_sismos
                Order by fechaEvento DESC;";
        try {

            $datos= $conn->executeQuery($sql);
            return $datos->fetchAllAssociative();
        }catch (Exception $e){
            return [];
        }


    }

    //    /**
    //     * @return TodosSismos[] Returns an array of TodosSismos objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TodosSismos
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
