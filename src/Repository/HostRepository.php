<?php

namespace App\Repository;

use App\Entity\Host;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Host>
 *
 * @method Host|null find($id, $lockMode = null, $lockVersion = null)
 * @method Host|null findOneBy(array $criteria, array $orderBy = null)
 * @method Host[]    findAll()
 * @method Host[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Host::class);
    }

    public function add(Host $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Host $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function updateHostBusinessClass($hostId, $businessClass)
    {
        $this->createQueryBuilder('host')
            ->update()
            ->set('host.level', ':businessClass')
            ->where('host.id = :hostId')
            ->setParameter('businessClass', $businessClass)
            ->setParameter('hostId', $hostId)
            ->getQuery()
            ->execute();
    }

    public function updateHostApprovalStatus($hostId, $approvalStatus)
    {
        $this->createQueryBuilder('host')
            ->update()
            ->set('host.approvalStatus', ':approvalStatus')
            ->where('host.id = :hostId')
            ->setParameter('approvalStatus', $approvalStatus)
            ->setParameter('hostId', $hostId)
            ->getQuery()
            ->execute();
    }

    public function getHostByAuthorizationStatus($array): array
    {
        $result = [];
        $baseQuery = $this->createQueryBuilder('host');
        foreach ($array as $filter => $value) {
            if (!is_null($value) and $value) {
                $baseQuery = $baseQuery->andWhere('host.approvalStatus = :approvalStatus')
                    ->setParameter('approvalStatus', strtoupper($filter));
                $result = array_merge($result, $baseQuery->getQuery()->getResult());
            }
        }
        return $result;
    }


//    /**
//     * @return Host[] Returns an array of Host objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Host
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
