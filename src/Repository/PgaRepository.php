<?php

namespace App\Repository;

use App\Entity\Pga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Exception;

/**
 * @extends ServiceEntityRepository<Pga>
 */
class PgaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pga::class);
    }


    /**
     * Obtener Todos los sismos registrados en el Seiscomp
     */
    public function findSismo(): ?array
    {
        $conn = $this->getEntityManager()->getConnection();
            $sql ="select distinct PEvent.publicID,Event._oid, Origin.time_value as hora, Origin._oid, ROUND(M.magnitude_value,1) as magnitud,
                ROUND(Origin.latitude_value,3) as latitud, ROUND(Origin.longitude_value,3) as longitud, ROUND(Origin.depth_value,2) as profundidad
                from Origin,PublicObject as POrigin,Event,PublicObject as PEvent, Magnitude as M
                where POrigin.publicID=Event.preferredOriginID and  M._parent_oid = Origin._oid
                and Origin._oid=POrigin._oid and Event._oid=PEvent._oid
                Order by Origin.time_value DESC;";
        try {

            $datos= $conn->executeQuery($sql);
            return $datos->fetchAllAssociative();
        }catch (Exception $e){
            return [];
        }


    }


    /**
     * Obtener Todos los sismos registrados en el Seiscomp
     */
    public function findPgaByEvento($evento): ?array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql ="select distinct Pga.idpga as id, Pga.estacion, ROUND(Pga.latitud,3) as latitud, ROUND(Pga.longitud,3) as longitud, ROUND(Pga.hne_pga,4) as hne, ROUND(Pga.hnn_pga,4) as hnn, ROUND(Pga.hnz_pga,4) as hnz,
                ROUND(Pga.maximo,4) as maximo, Pga.rutaWaveform as grafica
                From Pga Where Pga.tipo_estacion != 2 and Pga.nombre_evento = '".$evento."'";
        try {

            $datos= $conn->executeQuery($sql);
            return $datos->fetchAllAssociative();
        }catch (Exception $e){
            return [];
        }


    }


    //    /**
    //     * @return Pga[] Returns an array of Pga objects
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

    //    public function findOneBySomeField($value): ?Pga
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
