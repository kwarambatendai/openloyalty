<?php

namespace OpenLoyalty\Domain\Customer;

use OpenLoyalty\Domain\Identifier;
use Assert\Assertion as Assert;

/**
 * Class CampaignId.
 */
class CampaignId implements Identifier
{
    /**
     * @var string
     */
    protected $campaignId;

    /**
     * CampaignId constructor.
     *
     * @param string $campaignId
     */
    public function __construct($campaignId)
    {
        Assert::string($campaignId);
        Assert::uuid($campaignId);

        $this->campaignId = $campaignId;
    }

    public function __toString()
    {
        return $this->campaignId;
    }
}
