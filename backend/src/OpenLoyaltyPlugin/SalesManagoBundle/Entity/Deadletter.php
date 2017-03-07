<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyaltyPlugin\SalesManagoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="OpenLoyaltyPlugin\SalesManagoBundle\Entity\DeadletterRepository")
 * @ORM\Table(name="salesManagoDeadletter")
 */
class Deadletter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array")
     * @Assert\NotBlank()
     */
    private $message;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $contactEmail;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $ownerEmail;
    /**
     * @ORM\Column(type="integer")
     */
    private $retries = 1;

    /**
     * Deadletter constructor.
     *
     * @param string $ownerEmail
     * @param string $contactEmail
     * @param array  $data
     */
    public function __construct($ownerEmail, $contactEmail, $data)
    {
        $this->ownerEmail = $ownerEmail;
        $this->contactEmail = $contactEmail;
        $this->message = $data;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @return string
     */
    public function getOwnerEmail()
    {
        return $this->ownerEmail;
    }

    /**
     * @param string $ownerEmail
     */
    public function setOwnerEmail($ownerEmail)
    {
        $this->ownerEmail = $ownerEmail;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param int $retries
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;
    }
}
