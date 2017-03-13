<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\CQRS\Command;

use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AdminCommand.
 */
class AdminCommand
{
    public $firstName;

    public $lastName;

    public $phone;

    /**
     * @Assert\NotBlank()
     */
    public $email;

    /**
     * @var Admin
     */
    public $admin;
}
