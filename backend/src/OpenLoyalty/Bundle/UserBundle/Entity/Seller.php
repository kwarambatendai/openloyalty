<?php

namespace OpenLoyalty\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class Seller.
 *
 * @ORM\Entity()
 */
class Seller extends User
{
    public function __construct(SellerId $id)
    {
        parent::__construct($id->__toString());
    }
}
