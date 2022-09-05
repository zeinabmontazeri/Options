<?php

namespace App\Repository;

use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
    public function findByUserEvent_Id($userId, $eventId): int
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
        $query = $this->createQueryBuilder('o')
            ->select('o.id as orderId,order_event.id as eventId,order_experience.title as title,o.status as status')
            ->andWhere('o.user=:var1')
            ->setParameter('var1', $userId)
            ->innerJoin('o.event', 'order_event')
            ->innerJoin('order_event.experience', 'order_experience')
            ->getQuery()
            ->execute();
        return $query;
    }

    public function setOrderAsCheckedOut(int $invoiceId): bool
    {
        $order = $this->find($invoiceId);

        if (is_null($order)) {
            return false;
        }

        $order->setStatus(EnumOrderStatus::CHECKOUT);

        $this
            ->getEntityManager()
            ->persist($order);

        $this
            ->getEntityManager()
            ->flush();

        return true;
    }

    public function isEventOrderPurchasable(int $orderId): ?Order
    {
        $query = $this
            ->getEntityManager()
            ->createQuery("
                SELECT o
                FROM App\Entity\Order o
                LEFT JOIN o.event e
                WHERE o.status = :status
                    AND o.id = :orderId
                    AND e.startsAt > :today
                    AND e.status = :eventStatus
            ")
            ->setParameter('status', EnumOrderStatus::DRAFT)
            ->setParameter('orderId', $orderId)
            ->setParameter('today', new \DateTimeImmutable())
            ->setParameter('eventStatus', EnumEventStatus::PUBLISHED);

        try {
            $result = $query->getSingleResult();
        } catch (NonUniqueResultException $e) {
            throw new HttpException(
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                sprintf('Multiple orders with same id(%d)', $orderId),
            );
        } catch (NoResultException $e) {
            return null;
        }

        return $result;
    }
}
