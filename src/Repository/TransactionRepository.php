<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Transaction;
use App\Entity\TransactionCmdEnum;
use App\Entity\TransactionOriginEnum;
use App\Entity\TransactionStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function isEventOrderPurchasable(int $orderId): ?Order
    {
        $query = $this
            ->getEntityManager()
            ->createQuery("
                SELECT o, u
                FROM App\Entity\Order o
                LEFT JOIN o.event e
                LEFT JOIN o.user u
                WHERE o.status = 'draft'
                    AND o.id = :orderId
                    AND e.startsAt < :today
            ")
            ->setParameter('orderId', $orderId)
            ->setParameter('today', new \DateTimeImmutable('now', new \DateTimeZone('Asia/Tehran')));

        try {
            $result = $query->getSingleResult();
        } catch (NonUniqueResultException $e) {
            throw new \Exception(sprintf('Multiple orders with same id(%d)', $orderId));
        } catch (NoResultException $e) {
            return null;
        }

        return $result;
    }

    public function getTransactionHistory(
        int $invoiceId,
        TransactionOriginEnum $origin,
        TransactionCmdEnum $cmd
    ) {
        $query = $this
            ->getEntityManager()
            ->createQuery("
                SELECT t
                FROM App\Entity\Transaction t
                WHERE t.invoiceId = :invoiceId
                    AND t.command = :command
                    AND t.origin = :origin
            ")
            ->setParameter('invoiceId', $invoiceId)
            ->setParameter('command', $cmd->value)
            ->setParameter('origin', $origin->value);

        $result = $query->getResult();

        return $result;
    }

    public function getVerificationsForPayment(
        int $paymentTransactionId,
        int $invoiceId,
        TransactionOriginEnum $origin,
    ) {
        $query = $this
            ->getEntityManager()
            ->createQuery("
            SELECT t
            FROM App\Entity\Transaction t
            WHERE t.invoiceId = :invoiceId
                AND t.command = :command
                AND t.origin = :origin
                AND t.parentId = :paymentId
        ")
            ->setParameter('invoiceId', $invoiceId)
            ->setParameter('command', TransactionCmdEnum::Verify->value)
            ->setParameter('origin', $origin->value)
            ->setParameter('paymentId', $paymentTransactionId);

        $result = $query->getResult();

        return $result;
    }
}
