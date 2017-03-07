<?php

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
     * @Route(name="oloy.points.transfer.list", path="/points/transfer")
     * @Route(name="oloy.points.transfer.seller.list", path="/seller/points/transfer")
     * @Method("GET")
     * @Security("is_granted('LIST_POINTS_TRANSFERS')")
     *
     * @ApiDoc(
     *     name="get points transfers list",
     *     section="Points transfers",
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
     * @param Request $request
     * @Route(name="oloy.points.transfer.add", path="/points/transfer/add")
     * @Method("POST")
     * @Security("is_granted('ADD_POINTS')")
     * @ApiDoc(
     *     name="Add points",
     *     section="Points transfers",
     *     input={"class" = "OpenLoyalty\Bundle\PointsBundle\Form\Type\AddPointsFormType", "name" = "transfer"}
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
     * @param Request $request
     * @Route(name="oloy.points.transfer.spend", path="/points/transfer/spend")
     * @Method("POST")
     * @Security("is_granted('SPEND_POINTS')")
     * @ApiDoc(
     *     name="Add points",
     *     section="Points transfers",
     *     input={"class" = "OpenLoyalty\Bundle\PointsBundle\Form\Type\AddPointsFormType", "name" = "transfer"}
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
     * @param PointsTransferDetails $transfer
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.points.transfer.cancel", path="/points/transfer/{transfer}/cancel")
     * @Security("is_granted('CANCEL', transfer)")
     * @Method("POST")
     *
     * @ApiDoc(
     *     name="Cancel transfer",
     *     section="Points transfers"
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
