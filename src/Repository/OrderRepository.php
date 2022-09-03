<?php

namespace App\Repository;

use App\Entity\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Json;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByUserEventId($userId,$eventId): int
    {
        return intval($this->createQueryBuilder('o')
            ->select('o.id')
            ->where('o.user=:var1')
            ->andWhere('o.event=:var2')
            ->setParameter('var1', $userId)
            ->setParameter('var2', $eventId)
            ->getQuery()
            ->getResult());
    }

    public function getTotalRegisteredEvent($eventId): int
    {
        return  intval($this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->where('o.event=:var1')
            ->setParameter('var1', $eventId)
            ->getQuery()
            ->getSingleScalarResult());
    }

    public function getHostSalesForSetBusinessClas($fromDate, $toDate)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery
        (
            'SELECT h.id as hostId, COUNT(o.id) ordersCount, SUM(o.payablePrice) as totalSell
            FROM App\Entity\Host h, App\Entity\Order o
            INNER JOIN App\Entity\Event e WITH o.event = e.id
            INNER JOIN App\Entity\Experience ex WITH e.experience = ex.id
            WHERE h.id = ex.host AND (o.createdAt BETWEEN :fromDate AND :toDate) AND o.status = :status
            GROUP BY h.id ORDER BY totalSell DESC')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->setParameter('status', 'checkout');
        return $query->getResult();
    }
}
