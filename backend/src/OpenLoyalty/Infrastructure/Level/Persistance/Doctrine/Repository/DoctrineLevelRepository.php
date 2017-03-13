<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Level\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;

/**
 * Class DoctrineLevelRepository.
 */
class DoctrineLevelRepository extends EntityRepository implements LevelRepository
{
    public function byId(LevelId $levelId)
    {
        return parent::find($levelId);
    }

    public function findOneByRewardPercent($percent)
    {
        return $this->findOneBy(['reward.value' => $percent / 100]);
    }

    public function findAll()
    {
        return parent::findAll();
    }

    public function findAllActive()
    {
        $qb = $this->createQueryBuilder('l');
        $qb->andWhere('l.active = :true')->setParameter('true', true);

        return $qb->getQuery()->getResult();
    }

    public function save(Level $level)
    {
        $this->getEntityManager()->persist($level);
        $this->getEntityManager()->flush();
    }

    public function remove(Level $level)
    {
        $this->getEntityManager()->remove($level);
    }

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'ASC')
    {
        $qb = $this->createQueryBuilder('l');

        if ($sortField) {
            $qb->orderBy('l.'.$sortField, $direction);
        }
        if ($perPage) {
            $qb->setMaxResults($perPage);
            $qb->setFirstResult(($page - 1) * $perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countTotal()
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('count(l.levelId)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findLevelByConditionValueWithTheBiggestReward($conditionValue)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->andWhere('l.conditionValue <= :condVal')->setParameter('condVal', $conditionValue);
        $qb->andWhere('l.active = :true')->setParameter('true', true);
        $qb->orderBy('l.conditionValue', 'DESC');
        $qb->addOrderBy('l.reward.value', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findNextLevelByConditionValueWithTheBiggestReward($conditionValue, $currentLevelValue)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->andWhere('l.conditionValue > :condVal')->setParameter('condVal', $conditionValue);
        $qb->andWhere('l.active = :true')->setParameter('true', true);
        $qb->andWhere('l.conditionValue > :currentValue')->setParameter('currentValue', $currentLevelValue);
        $qb->orderBy('l.conditionValue', 'ASC');
        $qb->addOrderBy('l.reward.value', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
