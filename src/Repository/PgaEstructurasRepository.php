<?php

namespace App\Repository;

use App\Entity\PgaEstructuras;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PgaEstructuras>
 */
class PgaEstructurasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PgaEstructuras::class);
    }


    /**
     * @param string $evento
     * @param array $estaciones
     * @return Grafica[]
     */
    public function findGraficasByEventoYEstaciones(string $evento, array $estaciones): array
    {
        // Si el edificio no tiene estaciones, no buscamos nada para ahorrar recursos
        if (empty($estaciones)) {
            return [];
        }

        return $this->createQueryBuilder('g')
            ->andWhere('g.nombre_evento = :evento')
            ->andWhere('g.estacion IN (:estaciones)') // Magia de Doctrine aquí
            ->setParameter('evento', $evento)
            ->setParameter('estaciones', $estaciones)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PgaEstructuras[] Returns an array of PgaEstructuras objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PgaEstructuras
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
