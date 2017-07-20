<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\PointsBundle\Controller\Api;

use Broadway\ReadModel\RepositoryInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\PointsBundle\Form\Type\AddPointsFormType;
use OpenLoyalty\Bundle\PointsBundle\Form\Type\SpendPointsFormType;
use OpenLoyalty\Domain\Account\Command\AddPoints;
use OpenLoyalty\Domain\Account\Command\CancelPointsTransfer;
use OpenLoyalty\Domain\Account\Command\SpendPoints;
use OpenLoyalty\Domain\Account\Exception\CannotBeCanceledException;
use OpenLoyalty\Domain\Account\Exception\NotEnoughPointsException;
use OpenLoyalty\Domain\Account\Model\AddPointsTransfer;
use OpenLoyalty\Domain\Account\Model\PointsTransfer;
use OpenLoyalty\Domain\Account\Model\SpendPointsTransfer;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PointsTransferController.
 */
class PointsTransferController extends FOSRestController
{
    /**
     * List of all points transfers.
     *
     * @Route(name="oloy.points.transfer.list", path="/points/transfer")
     * @Route(name="oloy.points.transfer.seller.list", path="/seller/points/transfer")
     * @Method("GET")
     * @Security("is_granted('LIST_POINTS_TRANSFERS')")
     *
     * @ApiDoc(
     *     name="get points transfers list",
     *     section="Points transfers",
     *     parameters={
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
     *
     * @return \FOS\RestBundle\View\View
     * @QueryParam(name="customerFirstName", requirements="[a-zA-Z]+", nullable=true, description="firstName"))
     * @QueryParam(name="customerLastName", requirements="[a-zA-Z]+", nullable=true, description="lastName"))
     * @QueryParam(name="customerPhone", requirements="[a-zA-Z0-9\-]+", nullable=true, description="phone"))
     * @QueryParam(name="customerEmail", nullable=true, description="email"))
     * @QueryParam(name="customerId", nullable=true, description="customerId"))
     * @QueryParam(name="state", nullable=true, requirements="[a-zA-Z0-9\-]+", description="state"))
     * @QueryParam(name="type", nullable=true, requirements="[a-zA-Z0-9\-]+", description="type"))
     */
    public function listAction(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $this->get('oloy.user.param_manager')->stripNulls($paramFetcher->all(), true, false);
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        /** @var PointsTransferDetailsRepository $repo */
        $repo = $this->get('oloy.points.account.repository.points_transfer_details');

        $transfers = $repo->findByParametersPaginated(
            $params,
            false,
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $repo->countTotal($params, false);

        return $this->view([
            'transfers' => $transfers,
            'total' => $total,
        ], 200);
    }

    /**
     * Method allows to add points to customer.
     *
     * @param Request $request
     * @Route(name="oloy.points.transfer.add", path="/points/transfer/add")
     * @Method("POST")
     * @Security("is_granted('ADD_POINTS')")
     * @ApiDoc(
     *     name="Add points",
     *     section="Points transfers",
     *     input={"class" = "OpenLoyalty\Bundle\PointsBundle\Form\Type\AddPointsFormType", "name" = "transfer"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors",
     *       404="Returned whend there is no account attached to customer"
     *     }
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function addPointsAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('transfer', AddPointsFormType::class);
        $commandBus = $this->get('broadway.command_handling.command_bus');
        $uuidGenerator = $this->get('broadway.uuid.generator');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            /** @var RepositoryInterface $accountDetailsRepo */
            $accountDetailsRepo = $this->get('oloy.points.account.repository.account_details');
            $accounts = $accountDetailsRepo->findBy(['customerId' => $data['customer']]);

            /** @var AccountDetails $account */
            $account = reset($accounts);
            if (!$account instanceof AccountDetails) {
                throw new NotFoundHttpException();
            }

            $pointsTransferId = new PointsTransferId($uuidGenerator->generate());
            $command = new AddPoints(
                $account->getAccountId(),
                new AddPointsTransfer(
                    $pointsTransferId,
                    $data['points'],
                    null,
                    false,
                    null,
                    $data['comment'],
                    PointsTransfer::ISSUER_ADMIN
                )
            );
            $commandBus->dispatch($command);

            return $this->view($pointsTransferId);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to spend customer points.
     *
     * @param Request $request
     * @Route(name="oloy.points.transfer.spend", path="/points/transfer/spend")
     * @Method("POST")
     * @Security("is_granted('SPEND_POINTS')")
     * @ApiDoc(
     *     name="Add points",
     *     section="Points transfers",
     *     input={"class" = "OpenLoyalty\Bundle\PointsBundle\Form\Type\AddPointsFormType", "name" = "transfer"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors",
     *       404="Returned when there is no account attached to customer"
     *     }
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function spendPointsAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('transfer', SpendPointsFormType::class);
        $commandBus = $this->get('broadway.command_handling.command_bus');
        $uuidGenerator = $this->get('broadway.uuid.generator');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            /** @var RepositoryInterface $accountDetailsRepo */
            $accountDetailsRepo = $this->get('oloy.points.account.repository.account_details');
            $accounts = $accountDetailsRepo->findBy(['customerId' => $data['customer']]);
            if (count($accounts) == 0) {
                throw new NotFoundHttpException();
            }

            /** @var AccountDetails $account */
            $account = reset($accounts);
            if (!$account instanceof AccountDetails) {
                throw $this->createNotFoundException();
            }

            $pointsTransferId = new PointsTransferId($uuidGenerator->generate());
            $command = new SpendPoints(
                $account->getAccountId(),
                new SpendPointsTransfer(
                    $pointsTransferId,
                    $data['points'],
                    null,
                    false,
                    $data['comment'],
                    PointsTransfer::ISSUER_ADMIN
                )
            );
            try {
                $commandBus->dispatch($command);
            } catch (NotEnoughPointsException $e) {
                $form->get('points')->addError(new FormError('not enough points'));

                return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
            }

            return $this->view($pointsTransferId);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Cancel specific points transfer.
     *
     * @param PointsTransferDetails $transfer
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.points.transfer.cancel", path="/points/transfer/{transfer}/cancel")
     * @Security("is_granted('CANCEL', transfer)")
     * @Method("POST")
     *
     * @ApiDoc(
     *     name="Cancel transfer",
     *     section="Points transfers",
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when points transfer cannot be canceled",
     *       404="Returned when points transfer does not exist"
     *     }
     * )
     */
    public function cancelTransferAction(PointsTransferDetails $transfer)
    {
        $commandBus = $this->get('broadway.command_handling.command_bus');

        try {
            $commandBus->dispatch(
                new CancelPointsTransfer(
                    $transfer->getAccountId(),
                    $transfer->getPointsTransferId()
                )
            );
        } catch (CannotBeCanceledException $e) {
            return $this->view([
                'error' => 'this transfer cannot be canceled',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->view([], 200);
    }
}
