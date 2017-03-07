<?php

namespace OpenLoyalty\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Admin.
 *
 * @ORM\Entity(repositoryClass="OpenLoyalty\Bundle\UserBundle\Entity\Repository\DoctrineAdminRepository")
 * @UniqueEntity(fields={"email"}, message="This email is already taken", errorPath="email")
 */
class Admin extends User
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="first_name")
     * @JMS\Expose()
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="last_name")
     * @JMS\Expose()
     */
    protected $lastName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="phone")
     * @JMS\Expose()
     */
    protected $phone;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     * @JMS\Expose()
     */
    protected $external = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="api_key")
     * @JMS\Expose()
     */
    protected $apiKey;

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return bool
     */
    public function isExternal()
    {
        return $this->external;
    }

    /**
     * @param bool $external
     */
    public function setExternal($external)
    {
        $this->external = $external;
        if ($external) {
            $this->password = '';
        }
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }
}
