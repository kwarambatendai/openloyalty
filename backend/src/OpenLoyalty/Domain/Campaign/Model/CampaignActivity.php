<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\Model;

use Assert\Assertion as Assert;

/**
 * Class CampaignActivity.
 */
class CampaignActivity
{
    /**
     * @var bool
     */
    protected $allTimeActive = true;

    /**
     * @var \DateTime
     */
    protected $activeFrom;

    /**
     * @var \DateTime
     */
    protected $activeTo;

    public function __construct($allTimeActive, $activeFrom = null, $activeTo = null)
    {
        $this->allTimeActive = $allTimeActive;
        $this->activeFrom = $activeFrom;
        $this->activeTo = $activeTo;
    }

    /**
     * @return bool
     */
    public function isAllTimeActive()
    {
        return $this->allTimeActive;
    }

    /**
     * @param bool $allTimeActive
     */
    public function setAllTimeActive($allTimeActive)
    {
        $this->allTimeActive = $allTimeActive;
    }

    /**
     * @return \DateTime
     */
    public function getActiveFrom()
    {
        if ($this->allTimeActive) {
            return;
        }

        return $this->activeFrom;
    }

    /**
     * @param \DateTime $activeFrom
     */
    public function setActiveFrom($activeFrom)
    {
        $this->activeFrom = $activeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getActiveTo()
    {
        if ($this->allTimeActive) {
            return;
        }

        return $this->activeTo;
    }

    /**
     * @param \DateTime $activeTo
     */
    public function setActiveTo($activeTo)
    {
        $this->activeTo = $activeTo;
    }

    public static function validateRequiredData(array $data)
    {
        if (isset($data['allTimeActive']) && !$data['allTimeActive']) {
            Assert::keyIsset($data, 'activeFrom');
            Assert::keyIsset($data, 'activeTo');
            Assert::isInstanceOf($data['activeFrom'], \DateTime::class);
            Assert::isInstanceOf($data['activeTo'], \DateTime::class);
        }
    }
}
