<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function getExperiencerOrder($userId)
    {
        $query= $this->createQueryBuilder('o')
            ->select('o.id as orderId,order_event.id as eventId,order_experience.title as title,o.status as status')
            ->andWhere('o.user=:var1')
            ->setParameter('var1', $userId)
            ->innerJoin('o.event', 'order_event')
            ->innerJoin('order_event.experience','order_experience')
            ->getQuery()
            ->execute();
        return $query;
    }

    public function getCompletedOrdersByEvents(Event $event)
    {
        return $this->createQueryBuilder('o')
            ->select('o.id as orderId,order_event.id as eventId,order_experience.title as title,o.status as status')
            ->andWhere('order_event.id=:event_id')
            ->setParameter('event_id', $event->getId())
            ->andWhere("o.status='CHECKOUT'")
            ->innerJoin('o.event', 'order_event')
            ->innerJoin('order_event.experience','order_experience')
            ->getQuery()
            ->execute();
    }

    public function getCompletedOrdersByExperience(Experience $experience)
    {
        return $this->createQueryBuilder('o')
            ->select('o.id as orderId,order_event.id as eventId,order_experience.title as title,o.status as status')
            ->innerJoin('o.event', 'order_event')
            ->innerJoin('order_event.experience','order_experience')
            ->andWhere('order_experience.id=:experience_id')
            ->setParameter('experience_id', $experience->getId())
            ->andWhere("o.status='CHECKOUT'")
            ->getQuery()
            ->execute();
    }
}
