<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign;

use OpenLoyalty\Domain\Campaign\Model\CampaignActivity;
use OpenLoyalty\Domain\Campaign\Model\CampaignPhoto;
use OpenLoyalty\Domain\Campaign\Model\CampaignVisibility;
use OpenLoyalty\Domain\Campaign\Model\Coupon;
use Assert\Assertion as Assert;

/**
 * Class Campaign.
 */
class Campaign
{
    const REWARD_TYPE_DISCOUNT_CODE = 'discount_code';
    const REWARD_TYPE_VALUE_CODE = 'value code';
    const REWARD_TYPE_FREE_DELIVERY_CODE = 'free_delivery_code';
    const REWARD_TYPE_GIFT_CODE = 'gift_code';
    const REWARD_TYPE_EVENT_CODE = 'event_code';

    /**
     * @var CampaignId
     */
    protected $campaignId;

    /**
     * @var string
     */
    protected $reward;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $shortDescription;

    /**
     * @var string
     */
    protected $conditionsDescription;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var int
     */
    protected $costInPoints = 0; // 0 (free) or greater

    /**
     * @var LevelId[]
     */
    protected $levels = [];

    /**
     * @var bool
     */
    protected $singleCoupon = false;

    /**
     * @var SegmentId[]
     */
    protected $segments = [];

    /**
     * @var bool
     */
    protected $unlimited = false;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $limitPerUser;

    /**
     * @var Coupon[]
     */
    protected $coupons;

    /**
     * @var CampaignActivity
     */
    protected $campaignActivity;

    /**
     * @var CampaignVisibility
     */
    protected $campaignVisibility;

    /**
     * @var string
     */
    protected $usageInstruction;

    /**
     * @var CampaignPhoto
     */
    protected $campaignPhoto;

    public function __construct(CampaignId $campaignId, array $data = [])
    {
        $this->campaignId = $campaignId;
        $this->setFromArray($data);
    }

    public function setFromArray(array $data)
    {
        if (isset($data['reward'])) {
            $this->reward = $data['reward'];
        }

        if (isset($data['name'])) {
            $this->name = $data['name'];
        }

        if (isset($data['shortDescription'])) {
            $this->shortDescription = $data['shortDescription'];
        }

        if (isset($data['conditionsDescription'])) {
            $this->conditionsDescription = $data['conditionsDescription'];
        }

        if (isset($data['active'])) {
            $this->active = $data['active'];
        }

        if (isset($data['costInPoints'])) {
            $this->costInPoints = $data['costInPoints'];
        }

        if (isset($data['levels'])) {
            $this->levels = $data['levels'];
        }

        if (isset($data['segments'])) {
            $this->segments = $data['segments'];
        }

        if (isset($data['unlimited'])) {
            $this->unlimited = $data['unlimited'];
        }

        if (isset($data['limit'])) {
            $this->limit = $data['limit'];
        }

        if (isset($data['limitPerUser'])) {
            $this->limitPerUser = $data['limitPerUser'];
        }

        if (isset($data['coupons'])) {
            $this->coupons = $data['coupons'];
        }
        if (isset($data['singleCoupon'])) {
            $this->singleCoupon = $data['singleCoupon'];
        };

        if (isset($data['campaignActivity'])) {
            $this->campaignActivity = new CampaignActivity(
                isset($data['campaignActivity']['allTimeActive']) ? $data['campaignActivity']['allTimeActive'] : true,
                isset($data['campaignActivity']['activeFrom']) ? $data['campaignActivity']['activeFrom'] : null,
                isset($data['campaignActivity']['activeTo']) ? $data['campaignActivity']['activeTo'] : null
            );
        }

        if (isset($data['campaignVisibility'])) {
            $this->campaignVisibility = new CampaignVisibility(
                isset($data['campaignVisibility']['allTimeVisible']) ? $data['campaignVisibility']['allTimeVisible'] : true,
                isset($data['campaignVisibility']['visibleFrom']) ? $data['campaignVisibility']['visibleFrom'] : null,
                isset($data['campaignVisibility']['visibleTo']) ? $data['campaignVisibility']['visibleTo'] : null
            );
        }

        if (isset($data['usageInstruction'])) {
            $this->setUsageInstruction($data['usageInstruction']);
        }
    }

    /**
     * @return CampaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * @return string
     */
    public function getReward()
    {
        return $this->reward;
    }

    /**
     * @param string $reward
     */
    public function setReward($reward)
    {
        $this->reward = $reward;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return string
     */
    public function getConditionsDescription()
    {
        return $this->conditionsDescription;
    }

    /**
     * @param string $conditionsDescription
     */
    public function setConditionsDescription($conditionsDescription)
    {
        $this->conditionsDescription = $conditionsDescription;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getCostInPoints()
    {
        return $this->costInPoints;
    }

    /**
     * @return bool
     */
    public function isSingleCoupon()
    {
        return $this->singleCoupon;
    }

    /**
     * @param bool $singleCoupon
     */
    public function setSingleCoupon($singleCoupon)
    {
        $this->singleCoupon = $singleCoupon;
    }

    /**
     * @param int $costInPoints
     */
    public function setCostInPoints($costInPoints)
    {
        $this->costInPoints = $costInPoints;
    }

    /**
     * @return LevelId[]
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * @param LevelId[] $levels
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;
    }

    /**
     * @return SegmentId[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param SegmentId[] $segments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
    }

    /**
     * @return bool
     */
    public function isUnlimited()
    {
        return $this->unlimited;
    }

    /**
     * @param bool $unlimited
     */
    public function setUnlimited($unlimited)
    {
        $this->unlimited = $unlimited;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        if ($this->unlimited) {
            return;
        }

        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimitPerUser()
    {
        if ($this->unlimited) {
            return;
        }

        return $this->limitPerUser;
    }

    /**
     * @param int $limitPerUser
     */
    public function setLimitPerUser($limitPerUser)
    {
        $this->limitPerUser = $limitPerUser;
    }

    /**
     * @return Model\Coupon[]
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * @param Model\Coupon[] $coupons
     */
    public function setCoupons($coupons)
    {
        $this->coupons = $coupons;
    }

    /**
     * @return CampaignActivity
     */
    public function getCampaignActivity()
    {
        return $this->campaignActivity;
    }

    /**
     * @param CampaignActivity $campaignActivity
     */
    public function setCampaignActivity($campaignActivity)
    {
        $this->campaignActivity = $campaignActivity;
    }

    /**
     * @return CampaignVisibility
     */
    public function getCampaignVisibility()
    {
        return $this->campaignVisibility;
    }

    /**
     * @param CampaignVisibility $campaignVisibility
     */
    public function setCampaignVisibility($campaignVisibility)
    {
        $this->campaignVisibility = $campaignVisibility;
    }

    /**
     * @return string
     */
    public function getUsageInstruction()
    {
        return $this->usageInstruction;
    }

    /**
     * @param string $usageInstruction
     */
    public function setUsageInstruction($usageInstruction)
    {
        $this->usageInstruction = $usageInstruction;
    }

    public static function validateRequiredData(array $data)
    {
        Assert::keyIsset($data, 'reward');
        Assert::string($data['reward']);
        Assert::choice($data['reward'], [
            self::REWARD_TYPE_DISCOUNT_CODE,
            self::REWARD_TYPE_EVENT_CODE,
            self::REWARD_TYPE_FREE_DELIVERY_CODE,
            self::REWARD_TYPE_GIFT_CODE,
            self::REWARD_TYPE_VALUE_CODE,
        ]);
        Assert::keyIsset($data, 'name');
        Assert::keyIsset($data, 'levels');
        Assert::isArray($data['levels']);
        Assert::allIsInstanceOf($data['levels'], LevelId::class);
        Assert::keyIsset($data, 'segments');
        Assert::isArray($data['segments']);
        Assert::allIsInstanceOf($data['segments'], SegmentId::class);
        Assert::true(count($data['segments']) > 0 || count($data['levels']) > 0, 'There must be at least one level or one segment');
        if (!isset($data['unlimited']) || !$data['unlimited']) {
            Assert::keyIsset($data, 'limit');
            Assert::greaterOrEqualThan($data['limit'], 1);
            Assert::keyIsset($data, 'limitPerUser');
            Assert::greaterOrEqualThan($data['limitPerUser'], 1);
        }

        Assert::keyIsset($data, 'coupons');
        Assert::isArray($data['coupons']);
        Assert::allIsInstanceOf($data['coupons'], Coupon::class);
        Assert::keyIsset($data, 'campaignActivity');
        Assert::keyIsset($data, 'campaignVisibility');
        CampaignActivity::validateRequiredData($data['campaignActivity']);
        CampaignVisibility::validateRequiredData($data['campaignVisibility']);
    }

    public function getFlatLevels()
    {
        return array_map(function (LevelId $levelId) {
            return $levelId->__toString();
        }, $this->levels);
    }

    public function getFlatSegments()
    {
        return array_map(function (SegmentId $segmentId) {
            return $segmentId->__toString();
        }, $this->segments);
    }

    public function getFlatCoupons()
    {
        return array_map(function (Coupon $coupon) {
            return $coupon->getCode();
        }, $this->coupons);
    }

    /**
     * @return CampaignPhoto
     */
    public function getCampaignPhoto()
    {
        return $this->campaignPhoto;
    }

    /**
     * @param CampaignPhoto $campaignPhoto
     */
    public function setCampaignPhoto($campaignPhoto)
    {
        $this->campaignPhoto = $campaignPhoto;
    }
}
