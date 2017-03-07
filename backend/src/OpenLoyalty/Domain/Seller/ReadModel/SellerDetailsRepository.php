<?php

namespace OpenLoyalty\Domain\Seller\ReadModel;

use Broadway\ReadModel\RepositoryInterface;

interface SellerDetailsRepository extends RepositoryInterface
{
    public function findByParameters(array $params, $exact = true);

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countTotal(array $params = [], $exact = true);
}
