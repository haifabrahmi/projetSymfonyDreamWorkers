<?php

namespace App\Repository;

use App\Entity\Circuit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Circuit>
 *
 * @method Circuit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Circuit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Circuit[]    findAll()
 * @method Circuit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CircuitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Circuit::class);
    }

    public function save(Circuit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Circuit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByNom($txt)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery("SELECT c from APP\Entity\Circuit c where c.nomC like :txt OR c.nbrbusC like :txt")
            ->setParameter('txt', '%' . $txt . '%');
        return $query->getResult();
    }

    public function searchCircuit($query)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where($qb->expr()->orX(
            $qb->expr()->like('e.nomC', ':query'),
            $qb->expr()->like('e.nbrbusC', ':query')
        ));
        $qb->setParameter('query', '%'.$query.'%');

        return $qb->getQuery()->getResult();
    }

    public function sortByAscDate(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.horaireC', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function sortByDescDate(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.horaireC', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Circuit[] Returns an array of Circuit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Circuit
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
