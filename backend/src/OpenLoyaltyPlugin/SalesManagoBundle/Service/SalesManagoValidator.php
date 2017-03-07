<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Service;

use Doctrine\ORM\EntityRepository;
use OpenLoyaltyPlugin\SalesManagoBundle\Entity\Config;

class SalesManagoValidator
{
    /**
     * @param EntityRepository $manager
     *
     * @return bool
     */
    public static function verifySalesManagoEnabled(EntityRepository $manager)
    {
        try {
            $config = $manager->findAll()[0];
        } catch (\Exception $e) {
            return false;
        }
        if ($config instanceof Config && $config->getSalesManagoIsActive()) {
            return true;
        }

        return false;
    }
}
