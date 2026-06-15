<?php

namespace App\Repository;

use App\Entity\Espectros;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Espectros>
 *
 * @method Espectros|null find($id, $lockMode = null, $lockVersion = null)
 * @method Espectros|null findOneBy(array $criteria, array $orderBy = null)
 * @method Espectros[]    findAll()
 * @method Espectros[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EspectrosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Espectros::class);
    }

    /**
     * Guarda o actualiza un registro en la base de datos.
     */
    public function save(Espectros $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Elimina un registro de la base de datos.
     */
    public function remove(Espectros $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // =========================================================================
    // MÉTODOS PERSONALIZADOS (Ejemplos útiles para tu proyecto)
    // =========================================================================

    /**
     * Devuelve todos los registros espectrales de un evento en particular,
     * ordenados por la aceleración máxima (sa_max_cm_s2) de forma descendente.
     *
     * @return Espectros[] Returns an array of Espectros objects
     */
    public function findByEvento(string $nombreEvento): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nombre_evento = :val')
            ->setParameter('val', $nombreEvento)
            ->orderBy('e.sa_max_cm_s2', 'DESC') // Ordenar de mayor a menor impacto
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Busca el registro espectral de una estación específica en un evento específico.
     */
    public function findOneByEventoYEstacion(string $nombreEvento, string $estacion): ?Espectros
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nombre_evento = :evento')
            ->andWhere('e.estacion = :estacion')
            ->setParameter('evento', $nombreEvento)
            ->setParameter('estacion', $estacion)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }



    public function findEspectrosByEventoConNombre($evento): ?array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT DISTINCT 
            espectros.id_espectros as id, 
            espectros.estacion, 
            espectros.nombre_archivo as grafica,
            E.nombre as nombre,
            E.suelo as suelo,
            E.zona as zona,
            ROUND(espectros.latitud,3) as latitud, 
            ROUND(espectros.longitud,3) as longitud, 
            ROUND(espectros.sa_max,4) as max, 
            ROUND(espectros.periodo_max,2) as periodo, 
            ROUND(espectros.sa_02,2) as sa_02,
            ROUND(espectros.sa_10,2) as sa_10
        FROM 
            espectros 
        LEFT JOIN 
            lis.estaciones E ON E.estacion = espectros.estacion
        WHERE  
            espectros.nombre_evento = :evento
    ";

        // Ejecutamos usando parámetros para evitar SQL Injection
        $resultSet = $conn->executeQuery($sql, ['evento' => $evento]);

        // Devuelve un array asociativo
        return $resultSet->fetchAllAssociative();


    }


}