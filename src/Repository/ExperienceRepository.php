<?php

namespace App\Repository;

use App\Entity\Experience;
use App\Trait\findByPaginationTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Experience>
 *
 * @method Experience|null find($id, $lockMode = null, $lockVersion = null)
 * @method Experience|null findOneBy(array $criteria, array $orderBy = null)
 * @method Experience[]    findAll()
 * @method Experience[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExperienceRepository extends ServiceEntityRepository
{
    use findByPaginationTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }

    public function add(Experience $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Experience $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterExperience($array)
    {
        $baseQuery = $this->createQueryBuilder('experience');
        foreach ($array as $filter => $value) {
            if (!is_null($value) and $filter != 'purchasable') {
                $baseQuery = $baseQuery->andWhere($baseQuery->expr()->in('experience.'. "{$filter}", ":{$filter}"))
                    ->setParameter("{$filter}", json_decode($value));
            } else {
                if ($value) {
                    $baseQuery = $baseQuery->join('experience.events', 'events')
                        ->andwhere('events.capacity > 0')
                        ->andWhere('events.startsAt > :date')
                        ->setParameter('date', new \DateTime());
                }
            }
        }
        return $baseQuery->getQuery()->getResult();
    }

    public function getAllPaginated($perPage,$page)
    {
        $queryBuilder = $this->createQueryBuilder('entity');
        $queryBuilder
            ->setFirstResult(($page-1)*$perPage)
            ->setMaxResults($perPage);

        $query = $queryBuilder->getQuery()
            ->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);
        $paginator = new Paginator($query);
        $result['results'] = $paginator->getIterator();
        $result['total'] = $paginator->count();
        return $result;
    }

//    public function findByPaginated(array $criteria, ?array $orderBy = null, $page = 1, $perPage = 20)
//    {
//        $queryBuilder = $this->createQueryBuilder('entity');
//        foreach ($criteria as $key=>$c){
//            $queryBuilder->where(
//                $queryBuilder->expr()->eq("entity.$key",$c)
//            );
//        }
//        if($orderBy)
//            foreach ($orderBy as $o){
//                $queryBuilder->addOrderBy($o);
//            }
//
//        $queryBuilder
//            ->setFirstResult(($page-1)*$perPage)
//            ->setMaxResults($perPage);
//        $query = $queryBuilder->getQuery()
//            ->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);
//        $paginator = new Paginator($query);
//        $result['results'] = $paginator->getIterator();
//        $result['total'] = $paginator->count();
//        return $result;
//    }
//    }
//    /**
//     * @return Experience[] Returns an array of Experience objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Experience
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
