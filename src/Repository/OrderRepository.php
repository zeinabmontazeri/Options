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

    public function getTotalIncomeAnEvent(Event $event)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('SUM(o.payablePrice) as totalIncome')
            ->where('o.event = :event_id AND o.status = :success')
            ->setParameter('event_id', $event->getId())
            ->setParameter('success', EnumOrderStatus::SUCCESS->value);

        $res = $qb->getQuery()->getResult();
        return $res[0]['totalIncome'] ?: 0;
    }

    public function findUsersInfoAnEvent(Event $event)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select("u.id, CONCAT(CONCAT(u.firstName, ' '), u.lastName) as fullName, DATE_DIFF(CURRENT_DATE(), u.birthDate) /365 as age, u.gender")
            ->join('o.user', 'u')
            ->where('o.event = :event_id AND o.status = :success')
            ->andWhere('u.id = o.user')
            ->setParameter('event_id', $event->getId())
            ->setParameter('success', EnumOrderStatus::SUCCESS->value);

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
}
