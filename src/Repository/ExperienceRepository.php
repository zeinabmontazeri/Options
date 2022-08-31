<?php

namespace App\Repository;

use App\Entity\Experience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\QueryException;
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

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int $page
     * @param int $perPage
     * @return object[] The objects.
     */
    public function findByPaginated(array $criteria, ?array $orderBy = null, $page = 1, $perPage = 20)
    {
        $queryBuilder = $this->createQueryBuilder('entity');
        foreach ($criteria as $key=>$c){
            $queryBuilder->andWhere($queryBuilder->expr()->eq($key,$c));
        }
        if($orderBy)
            foreach ($orderBy as $o){
                $queryBuilder->addOrderBy($o);
            }

        $queryBuilder
            ->setFirstResult(($page-1)*$perPage)
            ->setMaxResults($perPage);
        $query = $queryBuilder->getQuery()
            ->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);
        $paginator = new Paginator($query);
        $result['results'] = $paginator->getIterator();
        $result['total'] = $paginator->count();
        return $result;


//        $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
//        $persister->loa
//        $data =  $persister->loadAll($criteria, $orderBy, $perPage, ($page-1)*$perPage);
//        dd($data);
    }
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
