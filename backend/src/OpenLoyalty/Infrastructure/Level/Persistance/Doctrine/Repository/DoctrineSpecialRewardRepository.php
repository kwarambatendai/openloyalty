<?php

namespace OpenLoyalty\Infrastructure\Level\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Level\SpecialReward;
use OpenLoyalty\Domain\Level\SpecialRewardId;
use OpenLoyalty\Domain\Level\SpecialRewardRepository;

/**
 * Class DoctrineSpecialRewardRepository.
 */
class DoctrineSpecialRewardRepository extends EntityRepository implements SpecialRewardRepository
{
    public function byId(SpecialRewardId $specialRewardId)
    {
        return parent::find($specialRewardId);
    }

    public function findAll()
    {
        return parent::findAll();
    }

    public function save(SpecialReward $specialReward)
    {
        $this->getEntityManager()->persist($specialReward);
        $this->getEntityManager()->flush();
    }

    public function remove(SpecialReward $specialReward)
    {
        $this->getEntityManager()->remove($specialReward);
    }
}
