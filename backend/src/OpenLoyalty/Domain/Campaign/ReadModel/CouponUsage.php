<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\ReadModel;

use Broadway\ReadModel\ReadModelInterface;
use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\Model\Coupon;

/**
 * Class CouponUsage.
 */
class CouponUsage implements ReadModelInterface, SerializableInterface
{
    /**
     * @var int
     */
    protected $usage;
    /**
     * @var CampaignId
     */
    protected $campaignId;

    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * CouponUsage constructor.
     *
     * @param CampaignId $campaignId
     * @param CustomerId $customerId
     * @param Coupon     $coupon
     * @param int        $usage
     */
    public function __construct(CampaignId $campaignId, CustomerId $customerId, Coupon $coupon, $usage = 1)
    {
        $this->campaignId = $campaignId;
        $this->customerId = $customerId;
        $this->coupon = $coupon;
        $this->usage = $usage;
    }

    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        if (isset($data['usage'])) {
            $usage = $data['usage'];
        } else {
            $usage = 1;
        }

        return new self(new CampaignId($data['campaignId']), new CustomerId($data['customerId']), new Coupon($data['coupon']), $usage);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'campaignId' => $this->campaignId->__toString(),
            'customerId' => $this->customerId->__toString(),
            'coupon' => $this->coupon->getCode(),
            'usage' => $this->getUsage(),
        ];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->campaignId->__toString().'_'.$this->customerId->__toString().'_'.$this->coupon->getCode();
    }

    /**
     * @return CampaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @return mixed
     */
    public function getUsage()
    {
        return $this->usage;
    }
}
