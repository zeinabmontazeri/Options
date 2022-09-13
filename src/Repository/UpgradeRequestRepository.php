<?php

namespace App\Repository;

use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\UpgradeRequest;
use App\Trait\findByPaginationTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UpgradeRequest>
 *
 * @method UpgradeRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UpgradeRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UpgradeRequest[]    findAll()
 * @method UpgradeRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UpgradeRequestRepository extends ServiceEntityRepository
{
    use findByPaginationTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UpgradeRequest::class);
    }

    public function add(UpgradeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UpgradeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPendingRequests($page=1,$perPage = 20){
        $query = $this->createQueryBuilder('u')
            ->where('u.status=:status')
            ->setParameter('status',EnumPermissionStatus::PENDING->value)
            ->setFirstResult(($page-1)*$perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);

        $paginator = new Paginator($query);
        $result['results'] = $paginator->getIterator();
        $result['current_page'] = $page;
        $result['per_page'] = $perPage;
        $result['total'] = $paginator->count();
        return $result;
    }

//    /**
//     * @return UpgradeRequest[] Returns an array of UpgradeRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UpgradeRequest
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
