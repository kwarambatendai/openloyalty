<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\CampaignBundle\Controller\Api;

use Broadway\CommandHandling\CommandBusInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\CampaignBundle\Exception\CampaignLimitException;
use OpenLoyalty\Bundle\CampaignBundle\Exception\NotEnoughPointsException;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\LevelId;
use OpenLoyalty\Domain\Campaign\SegmentId;
use OpenLoyalty\Domain\Customer\Command\BuyCampaign;
use OpenLoyalty\Domain\Customer\Command\ChangeCampaignUsage;
use OpenLoyalty\Domain\Customer\Model\CampaignPurchase;
use OpenLoyalty\Domain\Customer\Model\Coupon;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomerCampaignsController.
 *
 * @Security("is_granted('ROLE_PARTICIPANT')")
 */
class CustomerCampaignsController extends FOSRestController
{
    /**
     * Get all campaigns available for logged in customer.
     *
     * @Route(name="oloy.campaign.customer.available", path="/customer/campaign/available")
     * @Method("GET")
     * @Security("is_granted('LIST_CAMPAIGNS_AVAILABLE_FOR_ME')")
     *
     * @ApiDoc(
     *     name="get customer available campaigns list",
     *     section="Customer Campaign",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request $request
     * @View(serializerGroups={"customer", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function availableCampaigns(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);
        $customer = $this->getLoggedCustomer();
        $availablePoints = null;
        $customerSegments = $this->get('oloy.segment.read_model.repository.segmented_customers')
            ->findBy(['customerId' => $customer->getCustomerId()->__toString()]);
        $segments = array_map(function (SegmentedCustomers $segmentedCustomers) {
            return new SegmentId($segmentedCustomers->getSegmentId()->__toString());
        }, $customerSegments);

        $campaignRepository = $this->get('oloy.campaign.repository');
        $campaigns = $campaignRepository
            ->getVisibleCampaignsForLevelAndSegment(
                $segments,
                new LevelId($customer->getLevelId()->__toString()),
                null,
                null,
                $pagination->getSort(),
                $pagination->getSortDirection()
            );

        $campaigns = array_filter($campaigns, function (Campaign $campaign) use ($customer) {
            $usageLeft = $this->get('oloy.campaign.campaign_provider')->getUsageLeft($campaign);
            $usageLeftForCustomer = $this->get('oloy.campaign.campaign_provider')
                ->getUsageLeftForCustomer($campaign, $customer->getCustomerId()->__toString());

            return $usageLeft > 0 && $usageLeftForCustomer > 0 ? true : false;
        });

        $view = $this->view(
            [
                'campaigns' => array_slice($campaigns, ($pagination->getPage() - 1) * $pagination->getPerPage(), $pagination->getPerPage()),
                'total' => count($campaigns),
            ],
            200
        );
        $context = new Context();
        $context->setGroups(['Default']);
        $context->setAttribute('customerId', $customer->getCustomerId()->__toString());
        $view->setContext($context);

        return $view;
    }

    /**
     * Get all campaigns bought by logged in customer.
     *
     * @Route(name="oloy.campaign.customer.bought", path="/customer/campaign/bought")
     * @Method("GET")
     * @Security("is_granted('LIST_CAMPAIGNS_BOUGHT_BY_ME')")
     *
     * @ApiDoc(
     *     name="get customer bough campaigns list",
     *     section="Customer Campaign",
     *     parameters={
     *       {"name"="includeDetails", "dataType"="boolean", "required"=false},
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request $request
     * @View(serializerGroups={"customer", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function boughtCampaigns(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);
        $customer = $this->getLoggedCustomer();
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customer_details');
        if (count($customer->getCampaignPurchases()) == 0) {
            return $this->view(
                [
                    'campaigns' => [],
                    'total' => 0,
                ],
                200
            );
        }
        $campaigns = $repo
            ->findPurchasesByCustomerIdPaginated(
                $customer->getCustomerId(),
                $pagination->getPage(),
                $pagination->getPerPage(),
                $pagination->getSort(),
                $pagination->getSortDirection()
            );

        if ($request->get('includeDetails', false)) {
            $campaignRepo = $this->get('oloy.campaign.repository');

            $campaigns = array_map(function (CampaignPurchase $campaignPurchase) use ($campaignRepo) {
                $campaignPurchase->setCampaign($campaignRepo->byId(new CampaignId($campaignPurchase->getCampaignId()->__toString())));

                return $campaignPurchase;
            }, $campaigns);
        }

        return $this->view(
            [
                'campaigns' => $campaigns,
                'total' => $repo->countPurchasesByCustomerId($customer->getCustomerId()),
            ],
            200
        );
    }

    /**
     * Buy campaign by logged in customer.
     *
     * @Route(name="oloy.campaign.customer.buy", path="/customer/campaign/{campaign}/buy")
     * @Method("POST")
     * @Security("is_granted('BUY', campaign)")
     *
     * @ApiDoc(
     *     name="buy campaign",
     *     section="Customer Campaign",
     *     statusCodes={
     *       200="Returned when successful",
     *       400="With error 'No coupons left' returned when campaign cannot be bought because of lack of coupons. With error 'Not enough points' returned when campaign cannot be bought because of not enough points on customer account. With empty error returned when campaign limits exceeded."
     *     }
     * )
     *
     * @param Campaign $campaign
     * @View(serializerGroups={"customer", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function buyCampaign(Campaign $campaign)
    {
        $provider = $this->get('oloy.campaign.campaign_provider');
        $campaignValidator = $this->get('oloy.campaign.campaign_validator');

        if (!$campaignValidator->isCampaignActive($campaign) || !$campaignValidator->isCampaignVisible($campaign)) {
            throw $this->createNotFoundException();
        }
        /** @var CustomerDetails $customer */
        $customer = $this->getLoggedCustomer();

        try {
            $campaignValidator->validateCampaignLimits($campaign, new CustomerId($customer->getCustomerId()->__toString()));
        } catch (CampaignLimitException $e) {
            return $this->view(['error' => $e->getMessage()], 400);
        }

        try {
            $campaignValidator->checkIfCustomerHasEnoughPoints($campaign, new CustomerId($customer->getCustomerId()->__toString()));
        } catch (NotEnoughPointsException $e) {
            return $this->view(['error' => $e->getMessage()], 400);
        }

        $freeCoupons = $provider->getFreeCoupons($campaign);

        if (!$campaign->isSingleCoupon() && count($freeCoupons) == 0) {
            return $this->view(['error' => 'No coupons left'], 400);
        } elseif ($campaign->isSingleCoupon()) {
            $freeCoupons = $provider->getAllCoupons($campaign);
        }

        $coupon = new Coupon(reset($freeCoupons));

        /** @var CommandBusInterface $bus */
        $bus = $this->get('broadway.command_handling.command_bus');
        $bus->dispatch(
            new BuyCampaign(
                $customer->getCustomerId(),
                new \OpenLoyalty\Domain\Customer\CampaignId($campaign->getCampaignId()->__toString()),
                $campaign->getName(),
                $campaign->getCostInPoints(),
                $coupon
            )
        );

        $this->get('oloy.user.email_provider')->customerBoughtCampaign(
            $customer,
            $campaign,
            $coupon
        );

        return $this->view(['coupon' => $coupon]);
    }

    /**
     * Mark specific coupon as used/unused by customer.
     *
     * @Route(name="oloy.campaign.customer.coupon_usage", path="/customer/campaign/{campaign}/coupon/{coupon}")
     * @Method("POST")
     * @Security("is_granted('MARK_COUPON_AS_USED', campaign)")
     *
     * @ApiDoc(
     *     name="mark coupon as used",
     *     section="Customer Campaign",
     *     parameters={
     *      {"name"="used", "dataType"="true|false", "required"=true, "description"="True if mark as used, false otherwise"},
     *     },
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when parameter 'used' not provided",
     *       404="Returned when customer or campaign not found"
     *     }
     * )
     *
     * @param Request  $request
     * @param Campaign $campaign
     * @param string   $coupon
     * @View(serializerGroups={"customer", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function campaignCouponUsage(Request $request, Campaign $campaign, $coupon)
    {
        /** @var CustomerDetails $customer */
        $customer = $this->getLoggedCustomer();
        $used = $request->request->get('used', null);
        if ($used === null) {
            return $this->view(['errors' => 'field "used" is required'], 400);
        }

        if (is_string($used)) {
            $used = str_replace('"', '', $used);
            $used = str_replace("'", '', $used);
        }

        if ($used === 'false' || $used === '0' || $used === 0) {
            $used = false;
        }

        /** @var CommandBusInterface $bus */
        $bus = $this->get('broadway.command_handling.command_bus');
        $bus->dispatch(
            new ChangeCampaignUsage(
                $customer->getCustomerId(),
                new \OpenLoyalty\Domain\Customer\CampaignId($campaign->getCampaignId()->__toString()),
                new Coupon($coupon),
                $used
            )
        );

        return $this->view(['used' => $used]);
    }

    /**
     * @return CustomerDetails
     */
    protected function getLoggedCustomer()
    {
        /** @var User $user */
        $user = $this->getUser();
        $customer = $this->get('oloy.user.read_model.repository.customer_details')->find($user->getId());
        if (!$customer instanceof CustomerDetails) {
            throw $this->createNotFoundException();
        }

        return $customer;
    }
}
