<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\CustomerId;

interface CustomerDetailsRepository extends RepositoryInterface
{
    public function findByBirthdayAnniversary(\DateTime $from, \DateTime $to, $onlyActive = true);

    public function findByCreationAnniversary(\DateTime $from, \DateTime $to, $onlyActive = true);

    public function findByParameters(array $params, $exact = true);

    public function findByParametersPaginated(array $params, $exact = true, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countTotal(array $params = [], $exact = true);

    public function findPurchasesByCustomerIdPaginated(CustomerId $customerId, $page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countPurchasesByCustomerId(CustomerId $customerId);

    public function findOneByCriteria($criteria, $limit);

    public function findAllWithAverageTransactionAmountBetween($from, $to, $onlyActive = true);

    public function findAllWithTransactionAmountBetween($from, $to, $onlyActive = true);

    public function findAllWithTransactionCountBetween($from, $to, $onlyActive = true);

    public function sumAllByField($fieldName);
}
