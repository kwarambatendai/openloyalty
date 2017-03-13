<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\CampaignBundle\Service;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\Model\Coupon;
use OpenLoyalty\Domain\Campaign\ReadModel\CampaignUsage;
use OpenLoyalty\Domain\Campaign\ReadModel\CampaignUsageRepository;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsage;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsageRepository;
use OpenLoyalty\Domain\Customer\ReadModel\CustomersBelongingToOneLevel;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomers;

/**
 * Class CampaignProvider.
 */
class CampaignProvider
{
    /**
     * @var RepositoryInterface
     */
    protected $segmentedCustomersRepository;

    /**
     * @var RepositoryInterface
     */
    protected $customerBelongingToOneLevelRepository;

    /**
     * @var CouponUsageRepository
     */
    protected $couponUsageRepository;

    /**
     * @var CampaignValidator
     */
    protected $campaignValidator;

    /**
     * @var CampaignUsageRepository
     */
    private $campaignUsageRepository;

    /**
     * CampaignCustomersProvider constructor.
     *
     * @param RepositoryInterface     $segmentedCustomersRepository
     * @param RepositoryInterface     $customerBelongingToOneLevelRepository
     * @param CouponUsageRepository   $couponUsageRepository
     * @param CampaignValidator       $campaignValidator
     * @param CampaignUsageRepository $campaignUsageRepository
     */
    public function __construct(
        RepositoryInterface $segmentedCustomersRepository,
        RepositoryInterface $customerBelongingToOneLevelRepository,
        CouponUsageRepository $couponUsageRepository,
        CampaignValidator $campaignValidator,
        CampaignUsageRepository $campaignUsageRepository
    ) {
        $this->segmentedCustomersRepository = $segmentedCustomersRepository;
        $this->customerBelongingToOneLevelRepository = $customerBelongingToOneLevelRepository;
        $this->couponUsageRepository = $couponUsageRepository;
        $this->campaignValidator = $campaignValidator;
        $this->campaignUsageRepository = $campaignUsageRepository;
    }

    public function visibleForCustomers(Campaign $campaign)
    {
        if (!$this->campaignValidator->isCampaignVisible($campaign)) {
            return [];
        }

        // todo: check campaign limits?

        $customers = [];

        foreach ($campaign->getSegments() as $segmentId) {
            $segmented = $this->segmentedCustomersRepository->findBy(['segmentId' => $segmentId->__toString()]);
            /** @var SegmentedCustomers $segm */
            foreach ($segmented as $segm) {
                $customers[$segm->getCustomerId()->__toString()] = $segm->getCustomerId()->__toString();
            }
        }

        foreach ($campaign->getLevels() as $levelId) {
            $cst = $this->customerBelongingToOneLevelRepository->findBy(['levelId' => $levelId->__toString()]);
            /** @var CustomersBelongingToOneLevel $c */
            foreach ($cst as $c) {
                foreach ($c->getCustomers() as $cust) {
                    $customers[$cust['customerId']] = $cust['customerId'];
                }
            }
        }

        return $customers;
    }

    public function getAllCoupons(Campaign $campaign)
    {
        return array_map(function (Coupon $coupon) {
            return $coupon->getCode();
        }, $campaign->getCoupons());
    }

    public function getUsedCoupons(Campaign $campaign)
    {
        return array_map(function (CouponUsage $couponUsage) {
            return $couponUsage->getCoupon()->getCode();
        }, $this->couponUsageRepository->findByCampaign($campaign->getCampaignId()));
    }

    public function getFreeCoupons(Campaign $campaign)
    {
        return array_diff($this->getAllCoupons($campaign), $this->getUsedCoupons($campaign));
    }

    public function getUsageLeft(Campaign $campaign)
    {
        $used = $this->couponUsageRepository->countUsageForCampaign($campaign->getCampaignId());

        $usageLeft = $campaign->getLimit() - $used;
        if ($usageLeft < 0) {
            $usageLeft = 0;
        }
        $freeCoupons = $this->getCouponsUsageLeftCount($campaign);

        if ($campaign->isUnlimited()) {
            return $freeCoupons;
        } else {
            return min($freeCoupons, $usageLeft);
        }
    }

    public function getUsageLeftForCustomer(Campaign $campaign, $customerId)
    {
        $freeCoupons = $this->getCouponsUsageLeftCount($campaign);
        if (!$campaign->isSingleCoupon()) {
            $usageForCustomer = $this->couponUsageRepository->countUsageForCampaignAndCustomer($campaign->getCampaignId(), new CustomerId($customerId));
        } else {
            $campaignCoupon = $this->getAllCoupons($campaign);
            $coupon = $this->couponUsageRepository->find($campaign->getCampaignId().'_'.$customerId.'_'.reset($campaignCoupon));
            $usageForCustomer = $coupon ? $coupon->getUsage() : 0;
        }
        $usageLeftForCustomer = $campaign->getLimitPerUser() - $usageForCustomer;
        if ($usageLeftForCustomer < 0) {
            $usageLeftForCustomer = 0;
        }

        if ($campaign->isUnlimited()) {
            return $freeCoupons;
        } else {
            return min($freeCoupons, $usageLeftForCustomer);
        }
    }

    /**
     * @param Campaign $campaign
     *
     * @return int
     */
    protected function getCouponsUsageLeftCount($campaign)
    {
        if (!$campaign->isSingleCoupon()) {
            $freeCoupons = count($this->getFreeCoupons($campaign));
        } else {
            $usages = 0;
            $usagesRepo = $this->campaignUsageRepository->find($campaign->getCampaignId());
            if ($usagesRepo instanceof CampaignUsage) {
                $usages = $usagesRepo->getCampaignUsage();
            }
            if ($campaign->isUnlimited()) {
                $freeCoupons = PHP_INT_MAX;
            } else {
                $freeCoupons = ($campaign->getLimit() - $usages) < 0 ? 0 : $campaign->getLimit() - $usages;
            }
        }

        return $freeCoupons;
    }
}
