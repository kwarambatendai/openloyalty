<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Campaign\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CampaignRepository;
use OpenLoyalty\Domain\Campaign\LevelId;
use OpenLoyalty\Domain\Campaign\SegmentId;
use OpenLoyalty\Infrastructure\Campaign\Persistance\Doctrine\Functions\Cast;

/**
 * Class DoctrineCampaignRepository.
 */
class DoctrineCampaignRepository extends EntityRepository implements CampaignRepository
{
    public function findAll($returnQueryBuilder = false)
    {
        if ($returnQueryBuilder) {
            return $this->createQueryBuilder('e');
        }

        return parent::findAll();
    }

    public function byId(CampaignId $campaignId)
    {
        return parent::find($campaignId);
    }

    public function save(Campaign $campaign)
    {
        $this->getEntityManager()->persist($campaign);
        $this->getEntityManager()->flush();
    }

    public function remove(Campaign $campaign)
    {
        $this->getEntityManager()->remove($campaign);
    }

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'ASC')
    {
        $qb = $this->createQueryBuilder('e');

        if ($sortField) {
            $qb->orderBy('e.'.$sortField, $direction);
        }

        $qb->setMaxResults($perPage);
        $qb->setFirstResult(($page - 1) * $perPage);

        return $qb->getQuery()->getResult();
    }

    public function findAllVisiblePaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'ASC')
    {
        $qb = $this->createQueryBuilder('c');

        if ($sortField) {
            $qb->orderBy('c.'.$sortField, $direction);
        }

        $qb->andWhere(
            $qb->expr()->orX(
                'c.campaignVisibility.allTimeVisible = :true',
                $qb->expr()->andX(
                    'c.campaignVisibility.visibleFrom <= :now',
                    'c.campaignVisibility.visibleTo >= :now'
                )
            )
        );

        $qb->andWhere('c.active = :true')->setParameter('true', true);
        $qb->setParameter('now', new \DateTime());

        $qb->setMaxResults($perPage);
        $qb->setFirstResult(($page - 1) * $perPage);

        return $qb->getQuery()->getResult();
    }

    public function countTotal($onlyVisible = false)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('count(e.campaignId)');

        if ($onlyVisible) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'e.campaignVisibility.allTimeVisible = :true',
                    $qb->expr()->andX(
                        'e.campaignVisibility.visibleFrom <= :now',
                        'e.campaignVisibility.visibleTo >= :now'
                    )
                )
            );

            $qb->andWhere('e.active = :true')->setParameter('true', true);
            $qb->setParameter('now', new \DateTime());
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param SegmentId[] $segmentIds
     * @param LevelId     $levelId
     * @param int         $page
     * @param int         $perPage
     * @param null        $sortField
     * @param string      $direction
     *
     * @return \OpenLoyalty\Domain\Campaign\Campaign[]
     */
    public function getActiveCampaignsForLevelAndSegment(array $segmentIds = [], LevelId $levelId = null, $page = 1, $perPage = 10, $sortField = null, $direction = 'ASC')
    {
        $qb = $this->getCampaignsForLevelAndSegmentQueryBuilder($segmentIds, $levelId, $page, $perPage, $sortField, $direction);
        $qb->andWhere(
            $qb->expr()->orX(
                'c.campaignActivity.allTimeActive = :true',
                $qb->expr()->andX(
                    'c.campaignActivity.activeFrom <= :now',
                    'c.campaignActivity.activeTo >= :now'
                )
            )
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @param SegmentId[] $segmentIds
     * @param LevelId     $levelId
     * @param int         $page
     * @param int         $perPage
     * @param null        $sortField
     * @param string      $direction
     *
     * @return \OpenLoyalty\Domain\Campaign\Campaign[]
     */
    public function getVisibleCampaignsForLevelAndSegment(array $segmentIds = [], LevelId $levelId = null, $page = 1, $perPage = 10, $sortField = null, $direction = 'ASC')
    {
        $qb = $this->getCampaignsForLevelAndSegmentQueryBuilder($segmentIds, $levelId, $page, $perPage, $sortField, $direction);
        $qb->andWhere(
            $qb->expr()->orX(
                'c.campaignVisibility.allTimeVisible = :true',
                $qb->expr()->andX(
                    'c.campaignVisibility.visibleFrom <= :now',
                    'c.campaignVisibility.visibleTo >= :now'
                )
            )
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @param SegmentId[] $segmentIds
     * @param LevelId     $levelId
     * @param int         $page
     * @param int         $perPage
     * @param null        $sortField
     * @param string      $direction
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getCampaignsForLevelAndSegmentQueryBuilder(array $segmentIds = [], LevelId $levelId = null, $page = 1, $perPage = 10, $sortField = null, $direction = 'ASC')
    {
        $this->getEntityManager()->getConfiguration()->addCustomStringFunction('cast', Cast::class);
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.active = :true')->setParameter('true', true);

        $qb->setParameter('now', new \DateTime());
        $levelOrSegment = $qb->expr()->orX();
        if ($levelId) {
            $levelOrSegment->add($qb->expr()->like('cast(c.levels as text)', ':levelId'));
            $qb->setParameter('levelId', '%'.$levelId->__toString().'%');
        }

        $i = 0;
        foreach ($segmentIds as $segmentId) {
            $levelOrSegment->add($qb->expr()->like('cast(c.segments as text)', ':segmentId'.$i));
            $qb->setParameter('segmentId'.$i, '%'.$segmentId->__toString().'%');
            ++$i;
        }

        $qb->andWhere($levelOrSegment);

        if ($sortField) {
            $qb->orderBy('c.'.$sortField, $direction);
        }
        if ($perPage) {
            $qb->setMaxResults($perPage);
            $qb->setFirstResult(($page - 1) * $perPage);
        }

        return $qb;
    }
}
