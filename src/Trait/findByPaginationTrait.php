<?php
namespace App\Trait;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait findByPaginationTrait {

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int $page
     * @param int $perPage
     * @return object[] The objects.
     * @throws \Exception
     */
    public function findByPaginated(array $criteria, ?array $orderBy = null, $page = 1, $perPage = 20): array
    {
        $queryBuilder = $this->createQueryBuilder('entity');
        foreach ($criteria as $key=>$c){
            $queryBuilder->where(
                $queryBuilder->expr()->eq("entity.$key",$c)
            );
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
        $result['current_page'] = $page;
        $result['total'] = $paginator->count();
        return $result;
    }
}