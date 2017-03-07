<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Model;

use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Customer\CampaignId;

/**
 * Class CampaignPurchase.
 */
class CampaignPurchase implements SerializableInterface
{
    /**
     * @var \DateTime
     */
    protected $purchaseAt;

    /**
     * @var int
     */
    protected $costInPoints;

    /**
     * @var CampaignId
     */
    protected $campaignId;

    protected $campaign;

    /**
     * @var bool
     */
    protected $used = false;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * CampaignPurchase constructor.
     *
     * @param \DateTime  $purchaseAt
     * @param int        $costInPoints
     * @param CampaignId $campaignId
     */
    public function __construct(\DateTime $purchaseAt, $costInPoints, CampaignId $campaignId, Coupon $coupon)
    {
        $this->purchaseAt = $purchaseAt;
        $this->costInPoints = $costInPoints;
        $this->campaignId = $campaignId;
        $this->coupon = $coupon;
    }

    /**
     * @return \DateTime
     */
    public function getPurchaseAt()
    {
        return $this->purchaseAt;
    }

    /**
     * @return int
     */
    public function getCostInPoints()
    {
        return $this->costInPoints;
    }

    /**
     * @return CampaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    public static function deserialize(array $data)
    {
        $date = new \DateTime();
        $date->setTimestamp($data['purchaseAt']);

        $purchase = new self($date, $data['costInPoints'], new CampaignId($data['campaignId']), new Coupon($data['coupon']));
        $purchase->setUsed($data['used']);

        return $purchase;
    }

    public function serialize()
    {
        return [
            'costInPoints' => $this->costInPoints,
            'purchaseAt' => $this->purchaseAt->getTimestamp(),
            'campaignId' => $this->campaignId->__toString(),
            'coupon' => $this->coupon->getCode(),
            'used' => $this->used,
        ];
    }

    /**
     * @return bool
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @param bool $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * @return mixed
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param mixed $campaign
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }
}
