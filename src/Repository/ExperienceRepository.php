<?php

namespace App\Repository;

use App\Entity\Experience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
                $baseQuery = $baseQuery->andWhere($baseQuery->expr()->in('experience.' . "{$filter}", ":{$filter}"))
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

    public function getTrendingExperiences()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery('SELECT ex.id ,SUM(e.capacity - e.registeredUsers) as total_buyers
        FROM App\Entity\Experience ex
        INNER JOIN App\Entity\Event e
        WITH ex.id = e.experience
        GROUP BY ex.id
        HAVING COUNT((CASE WHEN e.capacity - e.registeredUsers > 0 and e.startsAt > :date then 1 else 0 end)) > 0')
            ->setParameter('date', new \DateTime());
        return $query->getResult();

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
