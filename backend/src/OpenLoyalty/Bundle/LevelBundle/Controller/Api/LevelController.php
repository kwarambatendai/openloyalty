<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\LevelBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\LevelBundle\Form\Type\LevelFormType;
use OpenLoyalty\Domain\Customer\ReadModel\CustomersBelongingToOneLevel;
use OpenLoyalty\Domain\Customer\ReadModel\CustomersBelongingToOneLevelRepository;
use OpenLoyalty\Domain\Level\Command\ActivateLevel;
use OpenLoyalty\Domain\Level\Command\CreateLevel;
use OpenLoyalty\Domain\Level\Command\DeactivateLevel;
use OpenLoyalty\Domain\Level\Command\UpdateLevel;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LevelController.
 */
class LevelController extends FOSRestController
{
    /**
     * Method allows to create new level.
     *
     * @param Request $request
     * @Route(name="oloy.level.create", path="/level/create")
     * @Method("POST")
     * @Security("is_granted('CREATE_LEVEL')")
     * @ApiDoc(
     *     name="Create new Level",
     *     section="Level",
     *     input={"class" = "OpenLoyalty\Bundle\LevelBundle\Form\Type\LevelFormType", "name" = "level"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors"
     *     }
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createLevelAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('level', LevelFormType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $levelId = new LevelId($this->get('broadway.uuid.generator')->generate());
            /** @var \OpenLoyalty\Bundle\LevelBundle\Model\Level $level */
            $level = $form->getData();
            $command = new CreateLevel($levelId, $level->toArray());
            $commandBus = $this->get('broadway.command_handling.command_bus');
            $commandBus->dispatch($command);

            if ($level->isActive()) {
                $commandBus->dispatch(new ActivateLevel($levelId));
            } else {
                $commandBus->dispatch(new DeactivateLevel($levelId));
            }

            return $this->view($levelId);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to edit existing level.
     *
     * @param Request $request
     * @param Level   $level
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.level.update", path="/level/{level}")
     * @Method("PUT")
     * @Security("is_granted('EDIT', level)")
     * @ApiDoc(
     *     name="Update Level",
     *     section="Level",
     *     input={"class" = "OpenLoyalty\Bundle\LevelBundle\Form\Type\LevelFormType", "name" = "level"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors"
     *     }
     * )
     */
    public function updateLevelAction(Request $request, Level $level)
    {
        $form = $this->get('form.factory')->createNamed('level', LevelFormType::class, null, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $command = new UpdateLevel($level->getLevelId(), $data->toArray());
            $commandBus = $this->get('broadway.command_handling.command_bus');
            $commandBus->dispatch($command);

            if ($data->isActive() !== $level->isActive()) {
                if ($data->isActive()) {
                    $commandBus->dispatch(new ActivateLevel($level->getLevelId()));
                } else {
                    $commandBus->dispatch(new DeactivateLevel($level->getLevelId()));
                }
            }

            return $this->view($level->getLevelId());
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method will return level details.
     *
     * @Route(name="oloy.level.get", path="/level/{level}")
     * @Route(name="oloy.level.seller.get", path="/seller/level/{level}")
     * @Method("GET")
     * @Security("is_granted('VIEW', level)")
     *
     * @ApiDoc(
     *     name="get Level",
     *     section="Level",
     *     statusCodes={
     *       200="Returned when successful",
     *       404="Returned when level does not exist"
     *     }
     * )
     *
     * @param Level $level
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getLevelAction(Level $level)
    {
        return $this->view(
            $level,
            200
        );
    }

    /**
     * Method will return list of customers assigned to this level.
     *
     * @Route(name="oloy.level.get_customers", path="/level/{level}/customers")
     * @Method("GET")
     * @Security("is_granted('LIST_CUSTOMERS', level)")
     *
     * @ApiDoc(
     *     name="get Level customers",
     *     section="Level",
     * )
     *
     * @param Request $request
     * @param Level   $level
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getLevelCustomersAction(Request $request, Level $level)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        /** @var CustomersBelongingToOneLevelRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customers_belonging_to_one_level');
        $levelId = new \OpenLoyalty\Domain\Customer\LevelId($level->getLevelId()->__toString());

        /** @var CustomersBelongingToOneLevel $levelCustomers */
        $levelCustomers = $repo->findByLevelIdPaginated(
            $levelId,
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );

        if (!$levelCustomers) {
            return $this->view(['customers' => []]);
        }

        return $this->view(
            [
                'customers' => $levelCustomers->getCustomers(),
                'total' => $repo->countByLevelId($levelId),
            ],
            200
        );
    }

    /**
     * Method will return complete list od levels.
     *
     * @Route(name="oloy.level.list", path="/level")
     * @Route(name="oloy.level.seller.list", path="/seller/level")
     * @Method("GET")
     * @Security("is_granted('LIST_LEVELS')")
     *
     * @ApiDoc(
     *     name="get Level list",
     *     section="Level",
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
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        $levelRepository = $this->get('oloy.level.repository');
        $levels = $levelRepository
            ->findAllPaginated(
                $pagination->getPage(),
                $pagination->getPerPage(),
                $pagination->getSort(),
                $pagination->getSortDirection()
            );
        $total = $levelRepository->countTotal();

        return $this->view(
            [
                'levels' => $levels,
                'total' => $total,
            ],
            200
        );
    }

    /**
     * Method allows to activate or deactivate level.
     *
     * @Route(name="oloy.level.activate", path="/level/{level}/activate")
     * @Method("POST")
     * @Security("is_granted('ACTIVATE', level)")
     *
     * @ApiDoc(
     *     name="activate/deactivate level",
     *     section="Level",
     *     parameters={{"name"="active", "dataType"="boolean", "required"=true}},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when active parameter is not present",
     *       404="Returned when level does not exist"
     *     }
     * )
     *
     * @param Request $request
     * @param Level   $level
     *
     * @return \FOS\RestBundle\View\View
     */
    public function activateLevelAction(Request $request, Level $level)
    {
        $activate = $request->request->get('active', null);
        if (null === $activate) {
            return $this->view(['active' => 'this field is required'], Response::HTTP_BAD_REQUEST);
        }

        $commandBus = $this->get('broadway.command_handling.command_bus');

        if ($activate) {
            $commandBus->dispatch(new ActivateLevel($level->getLevelId()));
        } else {
            $commandBus->dispatch(new DeactivateLevel($level->getLevelId()));
        }

        return $this->view();
    }
}
