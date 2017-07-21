<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CampaignId;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Model\Coupon;

/**
 * Class CampaignWasBoughtByCustomer.
 */
class CampaignWasBoughtByCustomer extends CustomerEvent
{
    /**
     * @var CampaignId
     */
    protected $campaignId;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var float
     */
    protected $costInPoints;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * @var string
     */
    protected $campaignName;

    public function __construct(CustomerId $customerId, CampaignId $campaignId, $campaignName, $costInPoints, Coupon $coupon)
    {
        parent::__construct($customerId);
        $this->campaignId = $campaignId;
        $this->createdAt = new \DateTime();
        $this->costInPoints = $costInPoints;
        $this->coupon = $coupon;
        $this->campaignName = $campaignName;
    }

    /**
     * @return CampaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    public function serialize()
    {
        return array_merge(
            parent::serialize(),
            [
                'campaignId' => $this->campaignId->__toString(),
                'costInPoints' => $this->costInPoints,
                'createdAt' => $this->createdAt->getTimestamp(),
                'coupon' => $this->coupon->getCode(),
                'campaignName' => $this->campaignName,
            ]
        );
    }

    public static function deserialize(array $data)
    {
        $bought = new self(new CustomerId($data['customerId']), new CampaignId($data['campaignId']), $data['campaignName'], $data['costInPoints'], new Coupon($data['coupon']));
        $date = new \DateTime();
        $date->setTimestamp($data['createdAt']);
        $bought->createdAt = $date;

        return $bought;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return float
     */
    public function getCostInPoints()
    {
        return $this->costInPoints;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @return string
     */
    public function getCampaignName()
    {
        return $this->campaignName;
    }
}
