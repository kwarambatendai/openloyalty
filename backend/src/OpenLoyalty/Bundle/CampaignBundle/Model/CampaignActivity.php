<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Model;

use OpenLoyalty\Domain\Campaign\Model\CampaignActivity as BaseCampaignActivity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CampaignActivity.
 */
class CampaignActivity extends BaseCampaignActivity
{
    /**
     * CampaignActivity constructor.
     */
    public function __construct()
    {
    }

    public function toArray()
    {
        return [
            'allTimeActive' => $this->allTimeActive,
            'activeFrom' => $this->activeFrom,
            'activeTo' => $this->activeTo,
        ];
    }

    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->allTimeActive) {
            return;
        }

        if (!$this->activeFrom) {
            $context->buildViolation((new NotBlank())->message)->atPath('activeFrom')->addViolation();
        }

        if (!$this->activeTo) {
            $context->buildViolation((new NotBlank())->message)->atPath('activeTo')->addViolation();
        }
    }
}
