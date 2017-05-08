<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign\ReadModel;

use Broadway\ReadModel\ReadModelInterface;
use Broadway\Serializer\SerializableInterface;
use OpenLoyalty\Domain\Campaign\CampaignId;

/**
 * Class CampaignUsage.
 */
class CampaignUsage implements ReadModelInterface, SerializableInterface
{
    /**
     * @var CampaignId
     */
    protected $campaignId;

    /**
     * @var int
     */
    protected $campaignUsage;

    /**
     * CampaignUsage constructor.
     *
     * @param CampaignId $campaignId
     */
    public function __construct(CampaignId $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->campaignId->__toString();
    }

    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $tmp = new self(new CampaignId($data['campaignId']));
        if (isset($data['usage'])) {
            $tmp->setCampaignUsage($data['usage']);
        }

        return $tmp;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'campaignId' => $this->campaignId->__toString(),
            'usage' => $this->campaignUsage,
        ];
    }

    /**
     * @return CampaignId
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * @return int
     */
    public function getCampaignUsage()
    {
        return $this->campaignUsage;
    }

    /**
     * @param int $campaignUsage
     */
    public function setCampaignUsage($campaignUsage)
    {
        $this->campaignUsage = $campaignUsage;
    }
}
