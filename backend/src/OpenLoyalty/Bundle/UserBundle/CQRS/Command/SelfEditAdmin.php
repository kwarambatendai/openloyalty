<?php

namespace OpenLoyalty\Bundle\UserBundle\CQRS\Command;

use OpenLoyalty\Bundle\UserBundle\Entity\Admin;

/**
 * Class SelfEditAdmin.
 */
class SelfEditAdmin extends AdminCommand
{
    /**
     * SelfEditAdmin constructor.
     *
     * @param Admin $admin
     */
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }
}
