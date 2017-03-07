<?php

namespace OpenLoyalty\Domain\Customer\Event;

use OpenLoyalty\Domain\Customer\CampaignId;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Model\Coupon;

/**
 * Class CampaignUsageWasChanged.
 */
class CampaignUsageWasChanged extends CustomerEvent
{
    /**
     * @var CampaignId
     */
    protected $campaignId;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * @var bool
     */
    protected $used;

    public function __construct(CustomerId $customerId, CampaignId $campaignId, Coupon $coupon, $used)
    {
        parent::__construct($customerId);
        $this->campaignId = $campaignId;
        $this->used = $used;
        $this->coupon = $coupon;
    }

    public function serialize()
    {
        return array_merge(
            parent::serialize(),
            [
                'campaignId' => $this->campaignId->__toString(),
                'used' => $this->used,
                'coupon' => $this->coupon->getCode(),
            ]
        );
    }

    public static function deserialize(array $data)
    {
        return new self(new CustomerId($data['customerId']), new CampaignId($data['campaignId']), new Coupon($data['coupon']), $data['used']);
    }

    /**
     * @return CampaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * @return bool
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }
}
