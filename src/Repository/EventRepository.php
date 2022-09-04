<?php

namespace App\Repository;

use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function add(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getEventsByExperienceId($experienceId)
    {
        return $this->createQueryBuilder('event')
            ->where('event.experience = :experienceId')
            ->setParameter('experienceId', $experienceId, Types::INTEGER)
            ->getQuery()
            ->getResult();
    }

    public function getTotalIncome(Event $event)
    {
        $qb = $this->getEntityManager()->getRepository('App\Entity\Order')->createQueryBuilder('o');
        $qb->select('SUM(o.payablePrice) as totalIncome')
            ->where('o.event = :event_id AND o.status = :checkout')
            ->setParameter('event_id', $event->getId())
            ->setParameter('checkout', EnumOrderStatus::CHECKOUT->value);

        $res = $qb->getQuery()->getResult();
        return $res[0]['totalIncome'] ?? 0;
    }

    public function findUsersInfoCheckoutedOrders(Event $event): array
    {
        $qb = $this->getEntityManager()->getRepository('App\Entity\Order')->createQueryBuilder('o');
        $qb->select("u.id, CONCAT(CONCAT(u.firstName, ' '), u.lastName) as fullName, DATE_DIFF(CURRENT_DATE(), u.birthDate) /365 as age, u.gender")
            ->join('o.user', 'u')
            ->where('o.event = :event_id AND o.status = :checkout')
            ->andWhere('u.id = o.user')
            ->setParameter('event_id', $event->getId())
            ->setParameter('checkout', EnumOrderStatus::CHECKOUT->value);

        $res = [];
        foreach ($qb->getQuery()->getArrayResult() as $userinfo) {
            $res[] = [
                'id' => $userinfo['id'],
                'fullName' => $userinfo['fullName'],
                'age' => (int)(floatval($userinfo['age'])),
                'gender' => $userinfo['gender']
            ];
        }
        return $res;
    }
//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
