<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\CampaignBundle\Event\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use OpenLoyalty\Bundle\CampaignBundle\Service\CampaignProvider;
use OpenLoyalty\Bundle\CampaignBundle\Service\CampaignValidator;
use OpenLoyalty\Bundle\UserBundle\Status\CustomerStatusProvider;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\ReadModel\CampaignUsageRepository;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsage;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsageRepository;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentId;
use OpenLoyalty\Domain\Segment\SegmentRepository;
use PhpOption\None;

/**
 * Class CampaignSerializationListener.
 */
class CampaignSerializationListener implements EventSubscriberInterface
{
    /**
     * @var CampaignValidator
     */
    protected $campaignValidator;

    /**
     * @var SegmentRepository
     */
    protected $segmentRepository;

    /**
     * @var LevelRepository
     */
    protected $levelRepository;

    /**
     * @var CouponUsageRepository
     */
    protected $couponUsageRepository;

    /**
     * @var CampaignProvider
     */
    protected $campaignProvider;
    /**
     * @var CampaignUsageRepository
     */
    private $campaignUsageRepository;

    /**
     * @var CustomerStatusProvider
     */
    private $customerStatusProvider;

    /**
     * CampaignSerializationListener constructor.
     *
     * @param CampaignValidator       $campaignValidator
     * @param SegmentRepository       $segmentRepository
     * @param LevelRepository         $levelRepository
     * @param CouponUsageRepository   $couponUsageRepository
     * @param CampaignProvider        $campaignProvider
     * @param CampaignUsageRepository $campaignUsageRepository
     * @param CustomerStatusProvider  $customerStatusProvider
     */
    public function __construct(
        CampaignValidator $campaignValidator,
        SegmentRepository $segmentRepository,
        LevelRepository $levelRepository,
        CouponUsageRepository $couponUsageRepository,
        CampaignProvider $campaignProvider,
        CampaignUsageRepository $campaignUsageRepository,
        CustomerStatusProvider $customerStatusProvider
    ) {
        $this->campaignValidator = $campaignValidator;
        $this->segmentRepository = $segmentRepository;
        $this->levelRepository = $levelRepository;
        $this->couponUsageRepository = $couponUsageRepository;
        $this->campaignProvider = $campaignProvider;
        $this->campaignUsageRepository = $campaignUsageRepository;
        $this->customerStatusProvider = $customerStatusProvider;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var Campaign $campaign */
        $campaign = $event->getObject();

        if ($campaign instanceof Campaign) {
            $segmentNames = [];
            $levelNames = [];

            foreach ($campaign->getSegments() as $segmentId) {
                $segment = $this->segmentRepository->byId(new SegmentId($segmentId->__toString()));
                if ($segment instanceof Segment) {
                    $segmentNames[$segmentId->__toString()] = $segment->getName();
                }
            }
            foreach ($campaign->getLevels() as $levelId) {
                $level = $this->levelRepository->byId(new LevelId($levelId->__toString()));
                if ($level instanceof Level) {
                    $levelNames[$levelId->__toString()] = $level->getName();
                }
            }

            if (in_array('admin', (array) $event->getContext()->attributes->get('groups'))) {
                $event->getVisitor()->addData('segmentNames', $segmentNames);
                $event->getVisitor()->addData('levelNames', $levelNames);
            }

            if (!$this->campaignValidator->isCampaignActive($campaign)) {
                if (!$campaign->getCampaignActivity()->isAllTimeActive()) {
                    $event->getVisitor()->addData('will_be_active_from', $campaign->getCampaignActivity()->getActiveFrom()->format(\DateTime::ISO8601));
                    $event->getVisitor()->addData('will_be_active_to', $campaign->getCampaignActivity()->getActiveTo()->format(\DateTime::ISO8601));
                }
            }

            $usageLeft = $this->campaignProvider->getUsageLeft($campaign);
            $event->getVisitor()->addData('usageLeft', $usageLeft);

            $context = $event->getContext();
            $option = $context->attributes->get('customerId');
            if ($option && !$option instanceof None) {
                $customerId = $context->attributes->get('customerId')->get();
                $usageLeftForCustomer = $this->campaignProvider->getUsageLeftForCustomer($campaign, $customerId);
                $event->getVisitor()->addData('usageLeftForCustomer', $usageLeftForCustomer);

                $customerStatus = $this->customerStatusProvider->getStatus(new \OpenLoyalty\Domain\Customer\CustomerId($customerId));
                $points = $customerStatus->getPoints();
                $canBuy = false;
                if ($points >= $campaign->getCostInPoints() && $this->campaignValidator->isCampaignActive($campaign)) {
                    $canBuy = true;
                }

                $event->getVisitor()->setData('canBeBoughtByCustomer', $canBuy);
            }

            $event->getVisitor()->addData('visibleForCustomersCount', count($this->campaignProvider->visibleForCustomers($campaign)));
            $event->getVisitor()->addData('usersWhoUsedThisCampaignCount', $this->countUsersWhoUsedThisCampaign($campaign));
        }
    }

    protected function countUsersWhoUsedThisCampaign(Campaign $campaign)
    {
        $usages = $this->couponUsageRepository->findByCampaign($campaign->getCampaignId());
        $users = [];
        /** @var CouponUsage $usage */
        foreach ($usages as $usage) {
            $users[$usage->getCustomerId()->__toString()] = true;
        }

        return count($users);
    }
}
