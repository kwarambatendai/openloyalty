<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\EarningRule\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\EarningRule\EarningRuleUsageId;
use OpenLoyalty\Domain\EarningRule\EarningRuleUsageRepository;
use OpenLoyalty\Domain\EarningRule\Model\UsageSubject;

/**
 * Class DoctrineEarningRuleUsageRepository.
 */
class DoctrineEarningRuleUsageRepository extends EntityRepository implements EarningRuleUsageRepository
{
    public function findAll($returnQueryBuilder = false)
    {
        if ($returnQueryBuilder) {
            return $this->createQueryBuilder('e');
        }

        return parent::findAll();
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

    public function byId(EarningRuleUsageId $earningRuleUsageId)
    {
        return parent::find($earningRuleUsageId);
    }

    public function countDailyUsage(EarningRuleId $earningRuleId, UsageSubject $subject)
    {
        $dayStart = new \DateTime();
        $dayStart->setTime(0, 0, 0);
        $dayEnd = new \DateTime();
        $dayEnd->setTime(23, 59, 59);

        return $this->findUsageByDates($earningRuleId, $subject, $dayStart, $dayEnd);
    }

    public function countWeeklyUsage(EarningRuleId $earningRuleId, UsageSubject $subject)
    {
        $start = new \DateTime();
        $start->modify('monday this week');
        $start->setTime(0, 0, 0);
        $end = new \DateTime();
        $end->modify('sunday this week');
        $end->setTime(23, 59, 59);

        return $this->findUsageByDates($earningRuleId, $subject, $start, $end);
    }

    public function countMonthlyUsage(EarningRuleId $earningRuleId, UsageSubject $subject)
    {
        $start = new \DateTime();
        $start->modify('first day of this month');
        $start->setTime(0, 0, 0);
        $end = new \DateTime();
        $end->modify('last day of this month');
        $end->setTime(23, 59, 59);

        return $this->findUsageByDates($earningRuleId, $subject, $start, $end);
    }

    protected function findUsageByDates(EarningRuleId $earningRuleId, UsageSubject $subject, \DateTime $from, \DateTime $to)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('count(u)');
        $qb->andWhere('u.earningRule = :earningRule')->setParameter('earningRule', $earningRuleId->__toString());
        $qb->andWhere('u.subject = :subject')->setParameter('subject', $subject->__toString());
        $qb->andWhere('u.date >= :start and u.date <= :end')
            ->setParameter('start', $from)
            ->setParameter('end', $to);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
