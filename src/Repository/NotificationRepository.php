<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function countNewNotificationsLast24h()
    {
        $qb = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.createdAt >= :date')
            ->setParameter('date', new \DateTime('-24 hours'));

        return $qb->getQuery()->getSingleScalarResult();
    }
}
