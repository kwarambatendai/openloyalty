<?php

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
