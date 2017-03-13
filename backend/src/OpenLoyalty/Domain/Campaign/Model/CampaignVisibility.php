<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\Model;

use Assert\Assertion as Assert;

/**
 * Class CampaignVisibility.
 */
class CampaignVisibility
{
    /**
     * @var bool
     */
    protected $allTimeVisible = true;

    /**
     * @var \DateTime
     */
    protected $visibleFrom;

    /**
     * @var \DateTime
     */
    protected $visibleTo;

    /**
     * CampaignVisibility constructor.
     *
     * @param bool      $allTimeVisible
     * @param \DateTime $visibleFrom
     * @param \DateTime $visibleTo
     */
    public function __construct($allTimeVisible, \DateTime $visibleFrom = null, \DateTime $visibleTo = null)
    {
        $this->allTimeVisible = $allTimeVisible;
        $this->visibleFrom = $visibleFrom;
        $this->visibleTo = $visibleTo;
    }

    /**
     * @return bool
     */
    public function isAllTimeVisible()
    {
        return $this->allTimeVisible;
    }

    /**
     * @param bool $allTimeVisible
     */
    public function setAllTimeVisible($allTimeVisible)
    {
        $this->allTimeVisible = $allTimeVisible;
    }

    /**
     * @return \DateTime
     */
    public function getVisibleFrom()
    {
        if ($this->allTimeVisible) {
            return;
        }

        return $this->visibleFrom;
    }

    /**
     * @param \DateTime $visibleFrom
     */
    public function setVisibleFrom($visibleFrom)
    {
        $this->visibleFrom = $visibleFrom;
    }

    /**
     * @return \DateTime
     */
    public function getVisibleTo()
    {
        if ($this->allTimeVisible) {
            return;
        }

        return $this->visibleTo;
    }

    /**
     * @param \DateTime $visibleTo
     */
    public function setVisibleTo($visibleTo)
    {
        $this->visibleTo = $visibleTo;
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
