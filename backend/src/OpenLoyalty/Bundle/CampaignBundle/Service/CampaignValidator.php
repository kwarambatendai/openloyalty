<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\CampaignBundle\Service;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Bundle\CampaignBundle\Exception\CampaignLimitExceededException;
use OpenLoyalty\Bundle\CampaignBundle\Exception\CampaignLimitPerCustomerExceededException;
use OpenLoyalty\Bundle\CampaignBundle\Exception\NoCouponsLeftException;
use OpenLoyalty\Bundle\CampaignBundle\Exception\NotEnoughPointsException;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsageRepository;

/**
 * Class CampaignValidator.
 */
class CampaignValidator
{
    /**
     * @var CouponUsageRepository
     */
    protected $couponUsageRepository;

    /**
     * @var RepositoryInterface
     */
    protected $accountDetailsRepository;

    /**
     * CampaignValidator constructor.
     *
     * @param CouponUsageRepository $couponUsageRepository
     * @param RepositoryInterface   $accountDetailsRepository
     */
    public function __construct(
        CouponUsageRepository $couponUsageRepository,
        RepositoryInterface $accountDetailsRepository
    ) {
        $this->couponUsageRepository = $couponUsageRepository;
        $this->accountDetailsRepository = $accountDetailsRepository;
    }

    public function validateCampaignLimits(Campaign $campaign, CustomerId $customerId)
    {
        $countUsageForCampaign = $this->couponUsageRepository->countUsageForCampaign($campaign->getCampaignId());

        if ($campaign->isUnlimited()) {
            if (!$campaign->isSingleCoupon() && $countUsageForCampaign >= count($campaign->getCoupons())) {
                throw new NoCouponsLeftException();
            }
        } else {
            if ($countUsageForCampaign >= $campaign->getLimit()) {
                throw new CampaignLimitExceededException();
            }
            $countUsageForCampaignAndCustomer = $this->couponUsageRepository->countUsageForCampaignAndCustomer(
                $campaign->getCampaignId(),
                $customerId
            );
            if ($countUsageForCampaignAndCustomer >= $campaign->getLimitPerUser()) {
                throw new CampaignLimitPerCustomerExceededException();
            }
        }
    }

    public function checkIfCustomerHasEnoughPoints(Campaign $campaign, CustomerId $customerId)
    {
        $accounts = $this->accountDetailsRepository->findBy(['customerId' => $customerId->__toString()]);
        if (count($accounts) == 0) {
            throw new NotEnoughPointsException();
        }
        /** @var AccountDetails $account */
        $account = reset($accounts);
        if ($account->getAvailableAmount() < $campaign->getCostInPoints()) {
            throw new NotEnoughPointsException();
        }
    }

    public function isCampaignActive(Campaign $campaign)
    {
        if (!$campaign->isActive()) {
            return false;
        }

        $campaignActivity = $campaign->getCampaignActivity();
        if ($campaignActivity->isAllTimeActive()) {
            return true;
        }
        $now = new \DateTime();
        if ($campaignActivity->getActiveFrom() <= $now && $now <= $campaignActivity->getActiveTo()) {
            return true;
        }

        return false;
    }

    public function isCampaignVisible(Campaign $campaign)
    {
        $campaignVisibility = $campaign->getCampaignVisibility();
        if ($campaignVisibility->isAllTimeVisible()) {
            return true;
        }
        $now = new \DateTime();
        if ($campaignVisibility->getVisibleFrom() <= $now && $now <= $campaignVisibility->getVisibleTo()) {
            return true;
        }

        return false;
    }
}
