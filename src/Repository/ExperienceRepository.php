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

    public function getExperiencesByHostId($hostId)
    {
        return $this->createQueryBuilder('experience')
            ->where('experience.host = :hostId')
            ->setParameter('hostId', $hostId)
            ->getQuery()
            ->getResult();
    }

    public function getExperienceByCategoryId($categoryId)
    {
        return $this->createQueryBuilder('experience')
            ->where('experience.category = :categoryId')
            ->setParameter("categoryId", $categoryId)
            ->getQuery()
            ->getResult();
    }




//    public function getEventList($experienceId)
//    {
////        return $this->createQueryBuilder('experience')
////            ->where('experience.id = :experienceId')
////            ->setParameter('experienceId', $experienceId)
////            ->select('experience.events')
////            ->getQuery()
////            ->getResult();
////    }
//
//        return $this->createQueryBuilder()
//            ->select('event')
//            ->from('App\Entity\Experience', 'experience')
//            ->where('experience.id = :experienceId')
//            ->setParameter('experienceId', $experienceId)
//            ->select('experience.events')
//            ->getQuery()
//            ->getResult();


//        return $this->createQueryBuilder('experience')
//            ->select('events')
//            ->leftJoin('experience.events', 'events')
//            ->where('experience.id = :experienceId')
//            ->setParameter('experienceId', $experienceId)
//            ->getQuery()
//            ->getResult();

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
