<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EarningRuleBundle\Controller\Api;

use Broadway\CommandHandling\CommandBusInterface;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\EarningRuleBundle\Form\Type\CreateEarningRuleFormType;
use OpenLoyalty\Bundle\EarningRuleBundle\Form\Type\EditEarningRuleFormType;
use OpenLoyalty\Domain\Account\CustomerId;
use OpenLoyalty\Domain\Account\SystemEvent\AccountSystemEvents;
use OpenLoyalty\Domain\Account\SystemEvent\CustomEventOccurredSystemEvent;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\EarningRule\Command\ActivateEarningRule;
use OpenLoyalty\Domain\EarningRule\Command\CreateEarningRule;
use OpenLoyalty\Domain\EarningRule\Command\DeactivateEarningRule;
use OpenLoyalty\Domain\EarningRule\Command\UpdateEarningRule;
use OpenLoyalty\Domain\EarningRule\Command\UseCustomEventEarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\EarningRule\Exception\CustomEventEarningRuleAlreadyExistsException;
use OpenLoyalty\Domain\EarningRule\Model\UsageSubject;
use OpenLoyalty\Infrastructure\Account\Exception\EarningRuleLimitExceededException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Version;
use OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRule as BundleEarningRule;

/**
 * Class EarningRuleController.
 */
class EarningRuleController extends FOSRestController
{
    /**
     * @Route(name="oloy.earning_rule.create", path="/earningRule")
     * @Method("POST")
     * @Security("is_granted('CREATE_EARNING_RULE')")
     * @ApiDoc(
     *     name="Create new Earning rule",
     *     section="Earning Rule",
     *     input={"class" = "OpenLoyalty\Bundle\EarningRuleBundle\Form\Type\CreateEarningRuleFormType", "name" = "earningRule"}
     * )
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('earningRule', CreateEarningRuleFormType::class);
        $uuidGenerator = $this->get('broadway.uuid.generator');

        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var \OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRule $data */
            $data = $form->getData();
            $id = new EarningRuleId($uuidGenerator->generate());

            try {
                $commandBus->dispatch(
                    new CreateEarningRule($id, $data->getType(), $data->toArray())
                );
            } catch (CustomEventEarningRuleAlreadyExistsException $e) {
                $form->get('eventName')->addError(new FormError($e->getMessage()));

                return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
            }

            return $this->view(['earningRuleId' => $id->__toString()]);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(name="oloy.earning_rule.edit", path="/earningRule/{earningRule}")
     * @Method("PUT")
     * @Security("is_granted('EDIT', earningRule)")
     * @ApiDoc(
     *     name="Edit Earning rule",
     *     section="Earning Rule",
     *     input={"class" = "OpenLoyalty\Bundle\EarningRuleBundle\Form\Type\EditEarningRuleFormType", "name" = "earningRule"}
     * )
     *
     * @param Request     $request
     * @param EarningRule $earningRule
     *
     * @return \FOS\RestBundle\View\View
     */
    public function editAction(Request $request, EarningRule $earningRule)
    {
        $model = BundleEarningRule::createFromDomain($earningRule);

        $form = $this->get('form.factory')
            ->createNamed(
                'earningRule',
                EditEarningRuleFormType::class,
                $model,
                [
                    'type' => array_flip(EarningRule::TYPE_MAP)[get_class($earningRule)],
                    'method' => 'PUT',
                ]
            );

        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var \OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRule $data */
            $data = $form->getData();

            try {
                $commandBus->dispatch(
                    new UpdateEarningRule($earningRule->getEarningRuleId(), $data->toArray())
                );
            } catch (CustomEventEarningRuleAlreadyExistsException $e) {
                $form->get('eventName')->addError(new FormError($e->getMessage()));

                return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
            }

            return $this->view(['earningRuleId' => $earningRule->getEarningRuleId()->__toString()]);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(name="oloy.earning_rule.get", path="/earningRule/{earningRule}")
     * @Route(name="oloy.earning_rule.seller.get", path="/seller/earningRule/{earningRule}")
     * @Method("GET")
     * @Security("is_granted('VIEW', earningRule)")
     * @ApiDoc(
     *     name="get Earning rule",
     *     section="Earning Rule"
     * )
     *
     * @param EarningRule $earningRule
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(EarningRule $earningRule)
    {
        return $this->view($earningRule);
    }

    /**
     * @Route(name="oloy.earning_rule.list", path="/earningRule")
     * @Route(name="oloy.earning_rule.seller.list", path="/seller/earningRule")
     * @Method("GET")
     * @Security("is_granted('LIST_ALL_EARNING_RULES')")
     *
     * @ApiDoc(
     *     name="get earning rules list",
     *     section="Earning Rule"
     * )
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getListAction(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        $earningRuleRepository = $this->get('oloy.earning_rule.repository');
        $rulesQb = $earningRuleRepository
            ->findAllPaginated(
                $pagination->getPage(),
                $pagination->getPerPage(),
                $pagination->getSort(),
                $pagination->getSortDirection(),
                true
            );
        $totalQb = $earningRuleRepository->countTotal(true);

        if ($request->query->has('active')) {
            $active = $request->get('active', null);
            if ($active == true) {
                $totalQb->andWhere($totalQb->getRootAliases()[0].'.active = true');
                $rulesQb->andWhere($totalQb->getRootAliases()[0].'.active = true');
            } elseif ($active == false) {
                $totalQb->andWhere($totalQb->getRootAliases()[0].'.active = false');
                $rulesQb->andWhere($totalQb->getRootAliases()[0].'.active = false');
            }
        }

        return $this->view(
            [
                'earningRules' => $rulesQb->getQuery()->getResult(),
                'total' => $totalQb->getQuery()->getSingleScalarResult(),
            ],
            200
        );
    }

    /**
     * @Route(name="oloy.earning_rule.activate", path="/earningRule/{earningRule}/activate")
     * @Method("POST")
     * @Security("is_granted('ACTIVATE', earningRule)")
     *
     * @ApiDoc(
     *     name="activate/deactivate earningRule",
     *     section="Earning Rule",
     *     parameters={{"name"="active", "dataType"="boolean", "required"=true}}
     * )
     *
     * @param Request     $request
     * @param EarningRule $earningRule
     *
     * @return \FOS\RestBundle\View\View
     */
    public function activateLevelAction(Request $request, EarningRule $earningRule)
    {
        $activate = $request->request->get('active', null);
        if (null === $activate) {
            return $this->view(['active' => 'this field is required'], Response::HTTP_BAD_REQUEST);
        }

        $commandBus = $this->get('broadway.command_handling.command_bus');

        if ($activate) {
            $commandBus->dispatch(new ActivateEarningRule($earningRule->getEarningRuleId()));
        } else {
            $commandBus->dispatch(new DeactivateEarningRule($earningRule->getEarningRuleId()));
        }

        return $this->view();
    }

    /**
     * @Route(name="oloy.earning_rule.report_custom_event", path="/{version}/earnRule/{eventName}/customer/{customer}", requirements={"version": "v1"}, defaults={"version":"v1"})
     * @Method("POST")
     *
     * @ApiDoc(
     *     name="report custom event and earn points",
     *     section="Earning Rule",
     *     parameters={{"name"="event_name", "dataType":"string", "required":true}},
     *     requirements={{"name"="version", "description"="api version, v1 required", "default":"v1"}}
     *
     * )
     *
     * @param $eventName
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     */
    public function reportCustomEventAction($eventName, CustomerDetails $customer)
    {
        $event = new CustomEventOccurredSystemEvent(new CustomerId($customer->getCustomerId()->__toString()),
            $eventName);

        try {
            $this->get('broadway.event_dispatcher')->dispatch(
                AccountSystemEvents::CUSTOM_EVENT_OCCURRED,
                [$event]
            );
        } catch (EarningRuleLimitExceededException $e) {
            return $this->view(['error' => 'limit exceeded'], Response::HTTP_BAD_REQUEST);
        }

        if ($event->getEvaluationResult() === null) {
            return $this->view(['error' => 'event does not exist'], Response::HTTP_BAD_REQUEST);
        }

        $this->get('broadway.command_handling.command_bus')
            ->dispatch(new UseCustomEventEarningRule(
                new EarningRuleId($event->getEvaluationResult()->getEarningRuleId()),
                new UsageSubject($customer->getCustomerId()->__toString())
            ));

        return $this->view(['points' => $event->getEvaluationResult()->getPoints()], Response::HTTP_OK);
    }
}
