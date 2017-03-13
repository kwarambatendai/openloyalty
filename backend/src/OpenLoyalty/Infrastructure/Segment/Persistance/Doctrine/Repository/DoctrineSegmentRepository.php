<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Segment\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentId;
use OpenLoyalty\Domain\Segment\SegmentRepository;

/**
 * Class DoctrineSegmentRepository.
 */
class DoctrineSegmentRepository extends EntityRepository implements SegmentRepository
{
    public function byId(SegmentId $segmentId)
    {
        return parent::find($segmentId);
    }

    public function findAll($returnQueryBuilder = false)
    {
        if ($returnQueryBuilder) {
            return $this->createQueryBuilder('e');
        }

        return parent::findAll();
    }

    public function findAllActive($returnQueryBuilder = false)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.active = :true')->setParameter('true', true);

        if ($returnQueryBuilder) {
            return $qb;
        }

        return $qb->getQuery()->getResult();
    }

    public function save(Segment $segment)
    {
        $this->getEntityManager()->persist($segment);
        $this->getEntityManager()->flush();
    }

    public function remove(Segment $segment)
    {
        $this->getEntityManager()->remove($segment);
        $this->getEntityManager()->flush($segment);
    }

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'ASC', $onlyActive = false)
    {
        $qb = $this->createQueryBuilder('l');

        if ($onlyActive) {
            $qb->andWhere('l.active = :true')->setParameter('true', true);
        }

        if ($sortField) {
            $qb->orderBy('l.'.$sortField, $direction);
        }
        $qb->setMaxResults($perPage);
        $qb->setFirstResult(($page - 1) * $perPage);

        return $qb->getQuery()->getResult();
    }

    public function countTotal()
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('count(l.segmentId)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
