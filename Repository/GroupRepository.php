<?php

namespace Plugin\ClaudeSample\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Eccube\Repository\AbstractRepository;
use Plugin\ClaudeSample\Entity\Group;

class GroupRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function getQueryBuilderBySearchData(array $searchData = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('g')
            ->orderBy('g.sortNo', Criteria::ASC);

        return $qb;
    }

    public function getMaxSortNo(): int
    {
        $qb = $this->createQueryBuilder('g')
            ->select('MAX(g.sortNo)')
            ->getQuery();

        $result = $qb->getSingleScalarResult();

        return $result ? (int) $result : 0;
    }
}
