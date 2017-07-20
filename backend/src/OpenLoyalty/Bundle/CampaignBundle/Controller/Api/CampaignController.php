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
use OpenLoyalty\Bundle\CampaignBundle\Form\Type\CampaignFormType;
use OpenLoyalty\Bundle\CampaignBundle\Form\Type\CampaignPhotoFormType;
use OpenLoyalty\Bundle\CampaignBundle\Form\Type\EditCampaignFormType;
use OpenLoyalty\Bundle\CampaignBundle\Model\Campaign;
use OpenLoyalty\Domain\Campaign\Campaign as DomainCampaign;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\Command\ChangeCampaignState;
use OpenLoyalty\Domain\Campaign\Command\CreateCampaign;
use OpenLoyalty\Domain\Campaign\Command\RemoveCampaignPhoto;
use OpenLoyalty\Domain\Campaign\Command\SetCampaignPhoto;
use OpenLoyalty\Domain\Campaign\Command\UpdateCampaign;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CampaignController.
 */
class CampaignController extends FOSRestController
{
    /**
     * Create new campaign.
     *
     * @Route(name="oloy.campaign.create", path="/campaign")
     * @Method("POST")
     * @Security("is_granted('CREATE_CAMPAIGN')")
     * @ApiDoc(
     *     name="Create new Campaign",
     *     section="Campaign",
     *     input={"class" = "OpenLoyalty\Bundle\CampaignBundle\Form\Type\CampaignFormType", "name" = "campaign"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when there are errors in form",
     *       404="Returned when campaign not found"
     *     }
     * )
     *
     * @param Request $request
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('campaign', CampaignFormType::class);
        $uuidGenerator = $this->get('broadway.uuid.generator');

        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Campaign $data */
            $data = $form->getData();
            $id = new CampaignId($uuidGenerator->generate());

            $commandBus->dispatch(
                new CreateCampaign($id, $data->toArray())
            );

            return $this->view(['campaignId' => $id->__toString()]);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Add photo to campaign.
     *
     * @Route(name="oloy.campaign.add_photo", path="/campaign/{campaign}/photo")
     * @Method("POST")
     * @Security("is_granted('EDIT', campaign)")
     * @ApiDoc(
     *     name="Add photo to Campaign",
     *     section="Campaign",
     *     input={"class" = "OpenLoyalty\Bundle\CampaignBundle\Form\Type\CampaignPhotoFormType", "name" = "photo"}
     * )
     *
     * @param Request        $request
     * @param DomainCampaign $campaign
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function addPhotoAction(Request $request, DomainCampaign $campaign)
    {
        $form = $this->get('form.factory')->createNamed('photo', CampaignPhotoFormType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->getData()->getFile();
            $uploader = $this->get('oloy.campaign.photo_uploader');
            $uploader->remove($campaign->getCampaignPhoto());
            $photo = $uploader->upload($file);
            $command = new SetCampaignPhoto($campaign->getCampaignId(), $photo);
            $this->get('broadway.command_handling.command_bus')->dispatch($command);

            return $this->view([], Response::HTTP_OK);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Remove photo from campaign.
     *
     * @Route(name="oloy.campaign.remove_photo", path="/campaign/{campaign}/photo")
     * @Method("DELETE")
     * @Security("is_granted('EDIT', campaign)")
     * @ApiDoc(
     *     name="Delete photo from Campaign",
     *     section="Campaign"
     * )
     *
     * @param DomainCampaign $campaign
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function removePhotoAction(DomainCampaign $campaign)
    {
        $uploader = $this->get('oloy.campaign.photo_uploader');
        $uploader->remove($campaign->getCampaignPhoto());

        $command = new RemoveCampaignPhoto($campaign->getCampaignId());
        $this->get('broadway.command_handling.command_bus')->dispatch($command);

        return $this->view([], Response::HTTP_OK);
    }

    /**
     * Get campaign photo.
     *
     * @Route(name="oloy.campaign.get_photo", path="/campaign/{campaign}/photo")
     * @Method("GET")
     * @ApiDoc(
     *     name="Get campaign photo",
     *     section="Campaign"
     * )
     *
     * @param Request        $request
     * @param DomainCampaign $campaign
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return Response
     */
    public function getPhotoAction(Request $request, DomainCampaign $campaign)
    {
        $photo = $campaign->getCampaignPhoto();
        if (!$photo) {
            throw $this->createNotFoundException();
        }
        $content = $this->get('oloy.campaign.photo_uploader')->get($photo);
        if (!$content) {
            throw $this->createNotFoundException();
        }

        $response = new Response($content);
        $response->headers->set('Content-Disposition', 'inline');
        $response->headers->set('Content-Type', $photo->getMime());

        return $response;
    }

    /**
     * Edit campaign.
     *
     * @Route(name="oloy.campaign.edit", path="/campaign/{campaign}")
     * @Method("PUT")
     * @Security("is_granted('EDIT', campaign)")
     * @ApiDoc(
     *     name="Create new Campaign",
     *     section="Campaign",
     *     input={"class" = "OpenLoyalty\Bundle\CampaignBundle\Form\Type\EditCampaignFormType", "name" = "campaign"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when there are errors in form",
     *       404="Returned when campaign not found"
     *     }
     * )
     *
     * @param Request        $request
     * @param DomainCampaign $campaign
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function editAction(Request $request, DomainCampaign $campaign)
    {
        $form = $this->get('form.factory')->createNamed('campaign', EditCampaignFormType::class, null, [
            'method' => 'PUT',
        ]);

        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Campaign $data */
            $data = $form->getData();
            $commandBus->dispatch(
                new UpdateCampaign($campaign->getCampaignId(), $data->toArray())
            );

            return $this->view(['campaignId' => $campaign->getCampaignId()->__toString()]);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Change campaign state action to active or inactive.
     *
     * @Route(name="oloy.campaign.change_state", path="/campaign/{campaign}/{active}", requirements={"active":"active|inactive"})
     * @Method("POST")
     * @Security("is_granted('EDIT', campaign)")
     * @ApiDoc(
     *     name="Change Campaign state",
     *     section="Campaign"
     * )
     *
     * @param DomainCampaign $campaign
     * @param                $active
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function changeStateAction(DomainCampaign $campaign, $active)
    {
        if ($active == 'active') {
            $campaign->setActive(true);
        } elseif ($active == 'inactive') {
            $campaign->setActive(false);
        }
        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');
        $commandBus->dispatch(
            new ChangeCampaignState($campaign->getCampaignId(), $campaign->isActive())
        );

        return $this->view(['campaignId' => $campaign->getCampaignId()->__toString()]);
    }

    /**
     * Get all campaigns.
     *
     * @Route(name="oloy.campaign.list", path="/campaign")
     * @Security("is_granted('LIST_ALL_CAMPAIGNS')")
     * @Method("GET")
     *
     * @ApiDoc(
     *     name="get campaigns list",
     *     section="Campaign",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request $request
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getListAction(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        $campaignRepository = $this->get('oloy.campaign.repository');
        $campaigns = $campaignRepository
            ->findAllPaginated(
                $pagination->getPage(),
                $pagination->getPerPage(),
                $pagination->getSort(),
                $pagination->getSortDirection()
            );
        $total = $campaignRepository->countTotal();

        $view = $this->view(
            [
                'campaigns' => $campaigns,
                'total' => $total,
            ],
            200
        );

        $context = new Context();
        $context->setGroups(['Default', 'list']);
        $view->setContext($context);

        return $view;
    }

    /**
     * Get all visible campaigns.
     *
     * @Route(name="oloy.campaign.seller.list", path="/seller/campaign")
     * @Security("is_granted('LIST_ALL_VISIBLE_CAMPAIGNS')")
     * @Method("GET")
     *
     * @ApiDoc(
     *     name="get campaigns list",
     *     section="Campaign",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request $request
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getVisibleListAction(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        $campaignRepository = $this->get('oloy.campaign.repository');
        $campaigns = $campaignRepository
            ->findAllVisiblePaginated(
                $pagination->getPage(),
                $pagination->getPerPage(),
                $pagination->getSort(),
                $pagination->getSortDirection()
            );
        $total = $campaignRepository->countTotal(true);

        $view = $this->view(
            [
                'campaigns' => $campaigns,
                'total' => $total,
            ],
            200
        );

        $context = new Context();
        $context->setGroups(['Default', 'list']);
        $view->setContext($context);

        return $view;
    }

    /**
     * Get single campaign details.
     *
     * @Route(name="oloy.campaign.get", path="/campaign/{campaign}")
     * @Route(name="oloy.campaign.seller.get", path="/seller/campaign/{campaign}")
     * @Method("GET")
     * @Security("is_granted('VIEW', campaign)")
     * @ApiDoc(
     *     name="get campaign details",
     *     section="Campaign"
     * )
     *
     * @param DomainCampaign $campaign
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(DomainCampaign $campaign)
    {
        return $this->view($campaign);
    }

    /**
     * Get customers who for whom this campaign is visible.
     *
     * @Route(name="oloy.campaign.get_customers_visible_for_campaign", path="/campaign/{campaign}/customers/visible")
     * @Method("GET")
     * @Security("is_granted('LIST_ALL_CAMPAIGNS')")
     *
     * @ApiDoc(
     *     name="campaign visible for customers",
     *     section="Campaign",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request        $request
     * @param DomainCampaign $campaign
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getVisibleForCustomersAction(Request $request, DomainCampaign $campaign)
    {
        $provider = $this->get('oloy.campaign.campaign_provider');
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        $customers = array_values($provider->visibleForCustomers($campaign));
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customer_details');
        $res = [];
        foreach ($customers as $id) {
            $tmp = $repo->find($id);
            if ($tmp instanceof CustomerDetails) {
                $res[] = $tmp;
            }
        }
        $total = count($res);
        $res = array_slice($res, ($pagination->getPage() - 1) * $pagination->getPerPage(), $pagination->getPerPage());

        return $this->view([
            'customers' => $res,
            'total' => $total,
        ]);
    }

    /**
     * List all campaigns that can be baught by this customer.
     *
     * @Route(name="oloy.campaign.admin.customer.available", path="/admin/customer/{customer}/campaign/available")
     * @Route(name="oloy.campaign.seller.customer.available", path="/seller/customer/{customer}/campaign/available")
     * @Method("GET")
     * @Security("is_granted('BUY_FOR_CUSTOMER')")
     *
     * @ApiDoc(
     *     name="get available campaigns for customer list",
     *     section="Campaign",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request         $request
     * @param CustomerDetails $customer
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function availableCampaigns(Request $request, CustomerDetails $customer)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        $customerSegments = $this->get('oloy.segment.read_model.repository.segmented_customers')
            ->findBy(['customerId' => $customer->getCustomerId()->__toString()]);
        $segments = array_map(function (SegmentedCustomers $segmentedCustomers) {
            return new SegmentId($segmentedCustomers->getSegmentId()->__toString());
        }, $customerSegments);

        $campaignRepository = $this->get('oloy.campaign.repository');
        $campaigns = $campaignRepository
            ->getVisibleCampaignsForLevelAndSegment(
                $segments,
                $customer->getLevelId() ? new LevelId($customer->getLevelId()->__toString()) : null,
                null,
                null,
                $pagination->getSort(),
                $pagination->getSortDirection()
            );
        $campaigns = array_filter($campaigns, function (DomainCampaign $campaign) use ($customer) {
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
     * Buy campaign.
     *
     * @Route(name="oloy.campaign.buy", path="/admin/customer/{customer}/campaign/{campaign}/buy")
     * @Route(name="oloy.campaign.seller.buy", path="/seller/customer/{customer}/campaign/{campaign}/buy")
     * @Method("POST")
     * @Security("is_granted('BUY_FOR_CUSTOMER')")
     *
     * @ApiDoc(
     *     name="buy campaign for customer",
     *     section="Campaign",
     *     statusCodes={
     *       200="Returned when successful",
     *       400="With error 'No coupons left' returned when campaign cannot be bought because of lack of coupons. With error 'Not enough points' returned when campaign cannot be bought because of not enough points on customer account. With empty error returned when campaign limits exceeded."
     *     }
     * )
     *
     * @param DomainCampaign  $campaign
     * @param CustomerDetails $customer
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function buyCampaign(DomainCampaign $campaign, CustomerDetails $customer)
    {
        $provider = $this->get('oloy.campaign.campaign_provider');
        $campaignValidator = $this->get('oloy.campaign.campaign_validator');

        if (!$campaignValidator->isCampaignActive($campaign) || !$campaignValidator->isCampaignVisible($campaign)) {
            throw $this->createNotFoundException();
        }

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
     * Get all campaigns bought by customer.
     *
     * @Route(name="oloy.campaign.admin.customer.bought", path="/admin/customer/{customer}/campaign/bought")
     * @Route(name="oloy.campaign.seller.customer.bought", path="/seller/customer/{customer}/campaign/bought")
     * @Method("GET")
     * @Security("is_granted('BUY_FOR_CUSTOMER')")
     *
     * @ApiDoc(
     *     name="get customer bough campaigns list",
     *     section="Customer Campaign",
     *     parameters={
     *      {"name"="includeDetails", "dataType"="boolean", "required"=false},
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request         $request
     * @param CustomerDetails $customer
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function boughtCampaigns(Request $request, CustomerDetails $customer)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

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
     * Mark specific coupon as used/unused by customer.
     *
     * @Route(name="oloy.campaign.admin.customer.coupon_usage", path="/admin/customer/{customer}/campaign/{campaign}/coupon/{coupon}")
     * @Method("POST")
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
     * @param Request         $request
     * @param CustomerDetails $customer
     * @param DomainCampaign  $campaign
     * @param string          $coupon
     * @View(serializerGroups={"admin", "Default"})
     *
     * @return \FOS\RestBundle\View\View
     */
    public function campaignCouponUsage(Request $request, CustomerDetails $customer, DomainCampaign $campaign, $coupon)
    {
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
}
