<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\EarningRule\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\EarningRule\CustomEventEarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\EarningRule\EarningRuleRepository;

/**
 * Class DoctrineEarningRuleRepository.
 */
class DoctrineEarningRuleRepository extends EntityRepository implements EarningRuleRepository
{
    public function findAll($returnQueryBuilder = false)
    {
        if ($returnQueryBuilder) {
            return $this->createQueryBuilder('e');
        }

        return parent::findAll();
    }

    public function byId(EarningRuleId $earningRuleId)
    {
        return parent::find($earningRuleId);
    }

    public function save(EarningRule $earningRule)
    {
        $this->getEntityManager()->persist($earningRule);
        $this->getEntityManager()->flush();
    }

    public function remove(EarningRule $earningRule)
    {
        $this->getEntityManager()->remove($earningRule);
    }

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'ASC', $returnQb = false)
    {
        $qb = $this->createQueryBuilder('e');

        if ($sortField) {
            $qb->orderBy('e.'.$sortField, $direction);
        }

        $qb->setMaxResults($perPage);
        $qb->setFirstResult(($page - 1) * $perPage);

        return $returnQb ? $qb : $qb->getQuery()->getResult();
    }

    public function countTotal($returnQb = false)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('count(e.earningRuleId)');

        return $returnQb ? $qb : $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllActive(\DateTime $date = null)
    {
        if (!$date) {
            $date = new \DateTime();
        }

        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.active = :true')->setParameter('true', true);
        $qb->andWhere($qb->expr()->orX(
            'e.allTimeActive = :true',
            'e.startAt <= :date AND e.endAt >= :date'
        ))->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    public function findAllActiveEventRules($eventName = null, \DateTime $date = null)
    {
        if (!$date) {
            $date = new \DateTime();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()->select('e');
        $qb->from('OpenLoyaltyDomainEarningRule:EventEarningRule', 'e');
        $qb->andWhere('e.active = :true')->setParameter('true', true);
        $qb->andWhere($qb->expr()->orX(
            'e.allTimeActive = :true',
            'e.startAt <= :date AND e.endAt >= :date'
        ))->setParameter('date', $date);

        if ($eventName) {
            $qb->andWhere('e.eventName = :eventName')->setParameter('eventName', $eventName);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByCustomEventName($eventName, \DateTime $date = null)
    {
        if (!$date) {
            $date = new \DateTime();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()->select('e');
        $qb->select('e')->from(CustomEventEarningRule::class, 'e');
        $qb->andWhere('e.active = :true')->setParameter('true', true);
        $qb->andWhere($qb->expr()->orX(
            'e.allTimeActive = :true',
            'e.startAt <= :date AND e.endAt >= :date'
        ))->setParameter('date', $date);
        $qb->andWhere('e.eventName = :eventName')->setParameter('eventName', $eventName);

        return $qb->getQuery()->getResult();
    }

    public function isCustomEventEarningRuleExist($eventName, $currentEarningRuleId = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('count(e)')
            ->from(CustomEventEarningRule::class, 'e');
        if ($currentEarningRuleId) {
            $qb->andWhere('e.earningRuleId != :earning_rule_id')
                ->setParameter('earning_rule_id', $currentEarningRuleId);
        }
        $qb->andWhere('e.eventName = :event_name')->setParameter('event_name', $eventName);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count > 0;
    }
}
