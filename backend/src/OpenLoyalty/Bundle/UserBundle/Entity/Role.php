<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="ol__roles")
 */
class Role implements RoleInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="role", unique=true, length=50)
     */
    private $role;

    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->role;
    }
}
