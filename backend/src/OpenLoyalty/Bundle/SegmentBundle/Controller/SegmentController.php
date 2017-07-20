<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SegmentBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\SegmentBundle\Form\Type\EditSegmentFormType;
use OpenLoyalty\Bundle\SegmentBundle\Form\Type\SegmentFormType;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Segment\Command\ActivateSegment;
use OpenLoyalty\Domain\Segment\Command\CreateSegment;
use OpenLoyalty\Domain\Segment\Command\DeactivateSegment;
use OpenLoyalty\Domain\Segment\Command\DeleteSegment;
use OpenLoyalty\Domain\Segment\Command\UpdateSegment;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomers;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomersRepository;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

/**
 * Class SegmentController.
 */
class SegmentController extends FOSRestController
{
    /**
     * Method allows to create new segment.
     *
     * @Route(name="oloy.segment.create", path="/segment")
     * @Method("POST")
     * @Security("is_granted('CREATE_SEGMENT')")
     * @ApiDoc(
     *     name="Create new segment",
     *     section="Segment",
     *     input={"class" = "OpenLoyalty\Bundle\SegmentBundle\Form\Type\SegmentFormType", "name" = "segment"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors",
     *     }
     * )
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('segment', SegmentFormType::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /* @var SegmentRepository $segmentRepository */
            if ($this->get('oloy.segment.segment_unique')->exists($form->getData()['name'])) {
                $form->get('name')->addError(new FormError('Segment with this name already exists'));

                return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
            }

            $segmentId = $this->get('broadway.uuid.generator')->generate();
            $this->get('broadway.command_handling.command_bus')
                ->dispatch(
                    new CreateSegment(
                        new SegmentId($segmentId),
                        $form->getData()
                    )
                );
            if ($form->getData()['active']) {
                $this->get('broadway.command_handling.command_bus')
                    ->dispatch(
                        new ActivateSegment(new SegmentId($segmentId))
                    );
            } else {
                $this->get('broadway.command_handling.command_bus')
                    ->dispatch(
                        new DeactivateSegment(new SegmentId($segmentId))
                    );
            }

            return $this->view(['segmentId' => $segmentId], 200);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to update segment data.
     *
     * @Route(name="oloy.segment.update", path="/segment/{segment}")
     * @Method("PUT")
     * @Security("is_granted('EDIT', segment)")
     * @ApiDoc(
     *     name="Update segment",
     *     section="Segment",
     *     input={"class" = "OpenLoyalty\Bundle\SegmentBundle\Form\Type\EditSegmentFormType", "name" = "segment"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors",
     *       404="Returned when segment does not exist"
     *     }
     * )
     *
     * @param Request $request
     * @param Segment $segment
     *
     * @return \FOS\RestBundle\View\View
     */
    public function editAction(Request $request, Segment $segment)
    {
        $form = $this->get('form.factory')->createNamed('segment', EditSegmentFormType::class, null, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($this->get('oloy.segment.segment_unique')->updateExists(
                $form->getData()['name'],
                $segment->getSegmentId()->__toString()
            )) {
                $form->get('name')->addError(new FormError('Segment with this name already exists'));

                return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
            }
            $this->get('broadway.command_handling.command_bus')
                ->dispatch(
                    new UpdateSegment(
                        $segment->getSegmentId(),
                        $form->getData()
                    )
                );
            if ($form->getData()['active']) {
                $this->get('broadway.command_handling.command_bus')
                    ->dispatch(
                        new ActivateSegment($segment->getSegmentId())
                    );
            } else {
                $this->get('broadway.command_handling.command_bus')
                    ->dispatch(
                        new DeactivateSegment($segment->getSegmentId())
                    );
            }

            return $this->view(['segmentId' => $segment->getSegmentId()->__toString()], 200);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to activate.
     *
     * @Route(name="oloy.segment.activate", path="/segment/{segment}/activate")
     * @Method("POST")
     * @Security("is_granted('ACTIVATE', segment)")
     * @ApiDoc(
     *     name="Activate segment",
     *     section="Segment",
     * )
     *
     * @param Segment $segment
     *
     * @return \FOS\RestBundle\View\View
     */
    public function activateAction(Segment $segment)
    {
        $this->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSegment($segment->getSegmentId())
            );

        return $this->view(null, 200);
    }

    /**
     * Method allows to deactivate segment.
     *
     * @Route(name="oloy.segment.deactivate", path="/segment/{segment}/deactivate")
     * @Method("POST")
     * @Security("is_granted('DEACTIVATE', segment)")
     * @ApiDoc(
     *     name="Deactivate segment",
     *     section="Segment"
     * )
     *
     * @param Segment $segment
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deactivateAction(Segment $segment)
    {
        $this->get('broadway.command_handling.command_bus')
            ->dispatch(
                new DeactivateSegment($segment->getSegmentId())
            );

        return $this->view(null, 200);
    }

    /**
     * Method allows to delete segment.
     *
     * @Route(name="oloy.segment.delete", path="/segment/{segment}")
     * @Method("DELETE")
     * @Security("is_granted('DELETE', segment)")
     * @ApiDoc(
     *     name="Delete segment",
     *     section="Segment"
     * )
     *
     * @param Segment $segment
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction(Segment $segment)
    {
        $this->get('broadway.command_handling.command_bus')
            ->dispatch(
                new DeleteSegment($segment->getSegmentId())
            );

        return $this->view(null, 200);
    }

    /**
     * Method will return segment details.
     *
     * @Route(name="oloy.segment.get", path="/segment/{segment}")
     * @Method("GET")
     * @Security("is_granted('VIEW', segment)")
     * @ApiDoc(
     *     name="Get segment",
     *     section="Segment"
     * )
     *
     * @param Segment $segment
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getSegmentAction(Segment $segment)
    {
        return $this->view($segment, 200);
    }

    /**
     * Method will return customers assigned to this segment.
     *
     * @Route(name="oloy.segment.get_customers", path="/segment/{segment}/customers")
     * @Method("GET")
     * @Security("is_granted('LIST_CUSTOMERS', segment)")
     * @ApiDoc(
     *     name="Get customers in segment",
     *     section="Segment",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     * @QueryParam(name="firstName", nullable=true, description="firstName"))
     * @QueryParam(name="lastName", nullable=true, description="lastName"))
     * @QueryParam(name="phone", requirements="[a-zA-Z0-9\-]+", nullable=true, description="phone"))
     * @QueryParam(name="email", nullable=true, description="email"))
     *
     * @param Request               $request
     * @param Segment               $segment
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getCustomersInSegmentAction(Request $request, Segment $segment, ParamFetcherInterface $paramFetcher)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);
        $params = $this->get('oloy.user.param_manager')->stripNulls($paramFetcher->all(), true, true);
        $params['segmentId'] = $segment->getSegmentId()->__toString();
        /** @var SegmentedCustomersRepository $repo */
        $repo = $this->get('oloy.segment.read_model.repository.segmented_customers');
        $segmented = $repo->findByParametersPaginated(
            $params,
            false,
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );

        $total = $repo->countTotal($params);

        if (count($segmented) == 0) {
            return $this->view([
                'customers' => [],
                'total' => $total,
            ]);
        }

        $view = $this->view(
            [
                'customers' => $segmented,
                'total' => $total,
            ]
        );
        $context = new Context();
        $context->addGroup('Default');
        $context->addGroup('customers_in_segment');
        $repo = $this->get('oloy.user.read_model.repository.customer_details');
        $serializer = $this->get('serializer');
        $customerDetails = [];
        /** @var SegmentedCustomers $seg */
        foreach ($segmented as $seg) {
            $details = $repo->find($seg->getCustomerId()->__toString());
            if ($details instanceof CustomerDetails) {
                $customerDetails[$seg->getCustomerId()->__toString()] = $serializer->toArray($details);
            }
        }
        $context->setAttribute('customersDetails', $customerDetails);

        $view->setContext($context);

        return $view;
    }

    /**
     * Method will return segments list.
     *
     * @Route(name="oloy.segment.list", path="/segment")
     * @Method("GET")
     * @Security("is_granted('LIST_SEGMENTS')")
     * @ApiDoc(
     *     name="Get segments list",
     *     section="Segment",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getListAction(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request, 'createdAt', 'DESC');
        $onlyActive = $request->get('active', null);

        $segmentRepository = $this->get('oloy.segment.repository');
        $segments = $segmentRepository
            ->findAllPaginated(
                $pagination->getPage(),
                $pagination->getPerPage(),
                $pagination->getSort(),
                $pagination->getSortDirection(),
                $onlyActive
            );
        $total = $segmentRepository->countTotal();

        return $this->view(
            [
                'segments' => $segments,
                'total' => $total,
            ],
            200
        );
    }
}
