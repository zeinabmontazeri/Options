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

    public function isInvoicePurchaced(int $invoiceId, TransactionOriginEnum $origin): bool
    {
        $query = $this
            ->getEntityManager()
            ->createQuery("
                SELECT paymentResponse
                FROM App\Entity\Transaction paymentResponse
                WHERE paymentResponse.command = :paymentResponseCommand
                AND paymentResponse.status = :paymentResponseStatus
                AND paymentResponse.parentId IN (
                    SELECT payment.id
                    FROM App\Entity\Transaction payment
                    WHERE payment.command = :paymentCommand
                    AND payment.status = :paymentStatus
                    AND payment.invoiceId = :invoceId
                    AND payment.origin = :origin
                )
            ")
            ->setParameter('paymentResponseCommand', TransactionCmdEnum::PaymentResponse)
            ->setParameter('paymentResponseStatus', TransactionStatusEnum::Success)
            ->setParameter('paymentCommand', TransactionCmdEnum::Payment)
            ->setParameter('paymentStatus', TransactionStatusEnum::Success)
            ->setParameter('invoceId', $invoiceId)
            ->setParameter('origin', $origin);

        $transactions = $query->getResult();

        return count($transactions) !== 0;
    }

    public function getPayment(
        TransactionStatusEnum $status,
        int $id,
        string $bankToken
    ): ?Transaction
    {
        $paymentTransaction = $this->transactionRepository->findOneBy([
            'command' => TransactionCmdEnum::Payment,
            'status' => $status,
            'id' => $id,
            'bankToken' => $bankToken,
        ]);

        return $paymentTransaction;
    }
}
