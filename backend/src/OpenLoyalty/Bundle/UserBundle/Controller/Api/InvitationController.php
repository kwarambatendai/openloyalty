<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\UserBundle\Form\Type\InvitationFormType;
use OpenLoyalty\Domain\Customer\Invitation;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Customer\ReadModel\InvitationDetailsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class InvitationController.
 **/
class InvitationController extends FOSRestController
{
    /**
     * @Route(name="oloy.user.invitation.list", path="/invitations")
     * @Method("GET")
     * @Security("is_granted('LIST_INVITATIONS')")
     *
     * @ApiDoc(
     *     name="Invitations list",
     *     section="Invitation",
     * )
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
     *
     * @return \FOS\RestBundle\View\View
     * @QueryParam(name="referrerId", nullable=true, description="referrerId"))
     * @QueryParam(name="referrerEmail", nullable=true, description="referrerEmail"))
     * @QueryParam(name="referrerName", nullable=true, description="referrerName"))
     * @QueryParam(name="recipientId", nullable=true, description="recipientId"))
     * @QueryParam(name="recipientEmail", nullable=true, description="recipientEmail"))
     * @QueryParam(name="recipientName", nullable=true, description="recipientName"))
     * @QueryParam(name="status", nullable=true, description="status"))
     */
    public function listAction(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $this->get('oloy.user.param_manager')->stripNulls($paramFetcher->all(), true, true);

        $pagination = $this->get('oloy.pagination')->handleFromRequest($request, 'referrerId', 'desc');

        /** @var InvitationDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.invitation_details');
        $invitations = $repo->findByParametersPaginated(
            $params,
            $request->get('strict', false),
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $repo->countTotal($params, $request->get('strict', false));

        return $this->view(
            [
                'invitations' => $invitations,
                'total' => $total,
            ],
            200
        );
    }

    /**
     * @Route(name="oloy.user.invitation.invite", path="/invitations/invite")
     * @Method("POST")
     * @Security("is_granted('INVITE')")
     *
     * @ApiDoc(
     *     name="Invite user",
     *     section="Invitation",
     * )
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function inviteAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('invitation', InvitationFormType::class);
        $form->handleRequest($request);

        /** @var CustomerDetailsRepository $customerDetailsRepo */
        $customerDetailsRepo = $this->get('oloy.user.read_model.repository.customer_details');
        /** @var User $user */
        $user = $this->getUser();
        $currentCustomer = $customerDetailsRepo->find($user->getId());

        if (!$currentCustomer instanceof CustomerDetails) {
            throw new AccessDeniedException();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $this->get('oloy.user.form_handler.invitation')->onSuccess($currentCustomer, $form);

            if ($response instanceof Response) {
                return $response;
            }

            return $this->view('', Response::HTTP_OK);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }
}
