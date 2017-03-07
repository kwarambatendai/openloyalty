<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyaltyPlugin\SalesManagoBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DeadletterRepository.
 *
 * @category    DivanteOpenLoyalty
 *
 * @author      Michal Kajszczak <mkajszczak@divante.pl>
 * @copyright   Copyright (C) 2016 Divante Sp. z o.o.
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class DeadletterRepository extends EntityRepository
{
    /**
     * @param Deadletter $deadletter
     */
    public function save(Deadletter $deadletter)
    {
        $this->getEntityManager()->persist($deadletter);
        $this->getEntityManager()->flush();
    }
}
