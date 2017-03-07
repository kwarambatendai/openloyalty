<?php

namespace OpenLoyalty\Domain\Segment\ReadModel;

use Broadway\ReadModel\RepositoryInterface;

interface SegmentedCustomersRepository extends RepositoryInterface
{
    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countTotal(array $params = [], $exact = true);
}
