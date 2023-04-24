<?php

namespace App\Repository;

use App\Entity\Bus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bus>
 *
 * @method Bus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bus[]    findAll()
 * @method Bus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bus::class);
    }

    public function save(Bus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByModele($modele)
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.modele LIKE :modele')
        ->setParameter('modele', '%' . $modele . '%')
        ->orderBy('b.modele', 'ASC')
        ->getQuery()
        ->getResult();
}
public function findByModeleDesc($modele)
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.modele LIKE :modele')
        ->setParameter('modele', '%' . $modele . '%')
        ->orderBy('b.modele', 'DESC')
        ->getQuery()
        ->getResult();
}
public function findAverageRatingForBus(Bus $bus): float
{
    $qb = $this->createQueryBuilder('b')
        ->select('AVG(r.rating) as avg_rating')
        ->leftJoin('b.ratings', 'r')
        ->where('b = :bus')
        ->setParameter('bus', $bus)
        ->getQuery();

    return (float) $qb->getSingleScalarResult();
}
public function busStatisticsByModel(string $model): array
{
    $entityManager = $this->getDoctrine()->getManager();
    $query = $entityManager->createQuery(
        'SELECT COUNT(b.id_bus) as totalBuses, AVG(b.capacite) as averageCapacity, MIN(b.date_depart) as firstDeparture, MAX(b.date_arrivee) as lastArrival
        FROM App\Entity\Bus b
        WHERE b.modele = :model'
    )->setParameter('model', $model);

    $result = $query->getResult()[0];

    return [
        'totalBuses' => $result['totalBuses'],
        'averageCapacity' => $result['averageCapacity'],
        'firstDeparture' => $result['firstDeparture'],
        'lastArrival' => $result['lastArrival']
    ];
}

    


//    /**
//     * @return Bus[] Returns an array of Bus objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bus
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
