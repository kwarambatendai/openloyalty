<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyaltyPlugin\SalesManagoBundle\DataProvider;

/**
 * Represents a data provided via OL.
 */
/**
 * Interface DataProviderInterface.
 */
interface DataProviderInterface
{
    /**
     * @param $data
     *
     * @return mixed
     */
    public function provideData($data);
}
