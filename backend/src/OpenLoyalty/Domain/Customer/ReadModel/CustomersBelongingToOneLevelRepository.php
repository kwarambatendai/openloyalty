<?php

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\LevelId;

interface CustomersBelongingToOneLevelRepository extends RepositoryInterface
{
    public function findByLevelIdPaginated(LevelId $levelId, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countByLevelId(LevelId $levelId);
}
