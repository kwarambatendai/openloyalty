<?php

namespace OpenLoyaltyPlugin\SalesManagoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="salesManagoConfig")
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $salesManagoIsActive;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $salesManagoApiEndpoint;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $salesManagoApiSecret;
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $salesManagoApiKey;
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $salesManagoCustomerId;
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $salesManagoOwnerEmail;

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
     * @return string
     */
    public function getSalesManagoIsActive()
    {
        return $this->salesManagoIsActive;
    }

    /**
     * @param string $salesManagoIsActive
     */
    public function setSalesManagoIsActive($salesManagoIsActive)
    {
        $this->salesManagoIsActive = $salesManagoIsActive;
    }

    /**
     * @return string
     */
    public function getSalesManagoApiEndpoint()
    {
        return $this->salesManagoApiEndpoint;
    }

    /**
     * @param string $salesManagoApiEndpoint
     */
    public function setSalesManagoApiEndpoint($salesManagoApiEndpoint)
    {
        $this->salesManagoApiEndpoint = $salesManagoApiEndpoint;
    }

    /**
     * @return string
     */
    public function getSalesManagoApiSecret()
    {
        return $this->salesManagoApiSecret;
    }

    /**
     * @param string $salesManagoApiSecret
     */
    public function setSalesManagoApiSecret($salesManagoApiSecret)
    {
        $this->salesManagoApiSecret = $salesManagoApiSecret;
    }

    /**
     * @return string
     */
    public function getSalesManagoApiKey()
    {
        return $this->salesManagoApiKey;
    }

    /**
     * @param string $salesManagoApiKey
     */
    public function setSalesManagoApiKey($salesManagoApiKey)
    {
        $this->salesManagoApiKey = $salesManagoApiKey;
    }

    /**
     * @return string
     */
    public function getSalesManagoCustomerId()
    {
        return $this->salesManagoCustomerId;
    }

    /**
     * @param string $salesManagoCustomerId
     */
    public function setSalesManagoCustomerId($salesManagoCustomerId)
    {
        $this->salesManagoCustomerId = $salesManagoCustomerId;
    }

    /**
     * @return string
     */
    public function getSalesManagoOwnerEmail()
    {
        return $this->salesManagoOwnerEmail;
    }

    /**
     * @param string $salesManagoOwnerEmail
     */
    public function setSalesManagoOwnerEmail($salesManagoOwnerEmail)
    {
        $this->salesManagoOwnerEmail = $salesManagoOwnerEmail;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'salesmanago' => [
                'endpoint' => $this->getSalesManagoApiEndpoint(),
                'apikey' => $this->getSalesManagoApiKey(),
                'apiSecret' => $this->getSalesManagoApiSecret(),
                'customerId' => $this->getSalesManagoCustomerId(),
                'salesmanagoActive' => $this->getSalesManagoIsActive(),
                'ownerEmail' => $this->getSalesManagoOwnerEmail(),
            ],
        ];
    }
}
