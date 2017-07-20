<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\UserBundle\Entity\Seller;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\UserBundle\Form\Type\SellerEditFormType;
use OpenLoyalty\Bundle\UserBundle\Form\Type\SellerRegistrationFormType;
use OpenLoyalty\Domain\Seller\Command\ActivateSeller;
use OpenLoyalty\Domain\Seller\Command\DeactivateSeller;
use OpenLoyalty\Domain\Seller\Command\DeleteSeller;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;
use OpenLoyalty\Domain\Seller\SellerId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SellerController.
 *
 * @Security("is_granted('ROLE_ADMIN')")
 */
class SellerController extends FOSRestController
{
    /**
     * Method will return list of sellers.
     *
     * @Route(name="oloy.user.seller.list", path="/seller")
     * @Method("GET")
     * @Security("is_granted('LIST_SELLERS')")
     *
     * @ApiDoc(
     *     name="Sellers list",
     *     section="Seller",
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
     * @QueryParam(name="firstName", nullable=true, description="firstName"))
     * @QueryParam(name="lastName", nullable=true, description="lastName"))
     * @QueryParam(name="phone", requirements="[a-zA-Z0-9\-]+", nullable=true, description="phone"))
     * @QueryParam(name="email", nullable=true, description="email"))
     */
    public function listAction(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $this->get('oloy.user.param_manager')->stripNulls($paramFetcher->all());
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        /** @var SellerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.seller_details');
        $sellers = $repo->findByParametersPaginated(
            $params,
            false,
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $repo->countTotal($params, false);

        return $this->view([
            'sellers' => $sellers,
            'total' => $total,
        ], 200);
    }

    /**
     * Method will return seller details.
     *
     * @Route(name="oloy.user.seller.get", path="/seller/{seller}")
     * @Method("GET")
     * @Security("is_granted('VIEW', seller)")
     *
     * @ApiDoc(
     *     name="Get seller",
     *     section="Seller",
     * )
     *
     * @param SellerDetails $seller
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getSellerAction(SellerDetails $seller)
    {
        return $this->view(
            $seller,
            200
        );
    }

    /**
     * Method allows to register new seller.
     *
     * @param Request $request
     * @Route(name="oloy.user.seller.register", path="/seller/register")
     * @Method("POST")
     * @Security("is_granted('CREATE_SELLER')")
     * @ApiDoc(
     *     name="Register Seller",
     *     section="Seller",
     *     input={"class" = "OpenLoyalty\Bundle\UserBundle\Form\Type\SellerRegistrationFormType", "name" = "seller"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors",
     *     }
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function registerSellerAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('seller', SellerRegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $sellerId = new SellerId($this->get('broadway.uuid.generator')->generate());

            $user = $this->get('oloy.user.form_handler.seller_registration')->onSuccess($sellerId, $form);

            if ($user instanceof User) {
                if ($form->getData()['active']) {
                    $this->get('broadway.command_handling.command_bus')
                        ->dispatch(
                            new ActivateSeller($sellerId)
                        );
                } else {
                    $this->get('broadway.command_handling.command_bus')
                        ->dispatch(
                            new DeactivateSeller($sellerId)
                        );
                }

                return $this->view([
                    'sellerId' => $sellerId->__toString(),
                    'password' => $user->getPlainPassword(),
                    'email' => $user->getEmail(),
                ]);
            }

            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to activate seller.
     *
     * @param SellerDetails $seller
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.seller.activate", path="/seller/{seller}/activate")
     * @Method("POST")
     * @Security("is_granted('ACTIVATE', seller)")
     * @ApiDoc(
     *     name="Activate Seller",
     *     section="Seller"
     * )
     */
    public function activateSellerAction(SellerDetails $seller)
    {
        $this->get('broadway.command_handling.command_bus')
            ->dispatch(
                new ActivateSeller($seller->getSellerId())
            );

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('OpenLoyaltyUserBundle:Seller')
            ->find($seller->getSellerId()->__toString());

        if ($user instanceof Seller) {
            $user->setIsActive(true);
            $this->get('oloy.user.user_manager')->updateUser($user);
        }

        return $this->view('', 200);
    }

    /**
     * Method allows to deactivate seller.
     *
     * @param SellerDetails $seller
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.seller.deactivate", path="/seller/{seller}/deactivate")
     * @Method("POST")
     * @Security("is_granted('DEACTIVATE', seller)")
     * @ApiDoc(
     *     name="Deactivate Seller",
     *     section="Seller"
     * )
     */
    public function deactivateSellerAction(SellerDetails $seller)
    {
        $this->get('broadway.command_handling.command_bus')
            ->dispatch(
                new DeactivateSeller($seller->getSellerId())
            );
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('OpenLoyaltyUserBundle:Seller')
            ->find($seller->getSellerId()->__toString());

        if ($user instanceof Seller) {
            $user->setIsActive(false);
            $this->get('oloy.user.user_manager')->updateUser($user);
        }

        return $this->view('', 200);
    }

    /**
     * Method allows to delete seller.
     *
     * @param SellerDetails $seller
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.seller.delete", path="/seller/{seller}/delete")
     * @Method("POST")
     * @Security("is_granted('DELETE', seller)")
     * @ApiDoc(
     *     name="Delete Seller",
     *     section="Seller"
     * )
     */
    public function deleteSellerAction(SellerDetails $seller)
    {
        $this->get('broadway.command_handling.command_bus')
            ->dispatch(
                new DeleteSeller($seller->getSellerId())
            );

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('OpenLoyaltyUserBundle:Seller')
            ->find($seller->getSellerId()->__toString());

        if ($user instanceof Seller) {
            $user->setIsActive(false);
            $user->setDeletedAt(new \DateTime());
            $this->get('oloy.user.user_manager')->updateUser($user);
        }

        return $this->view('', 200);
    }

    /**
     * Method allows to update seller details.
     *
     * @param Request       $request
     * @param SellerDetails $seller
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.seller.edit", path="/seller/{seller}")
     * @Method("PUT")
     * @Security("is_granted('EDIT', seller)")
     * @ApiDoc(
     *     name="Edit Seller",
     *     section="Seller",
     *     input={"class" = "OpenLoyalty\Bundle\UserBundle\Form\Type\SellerEditFormType", "name" = "seller"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors",
     *     }
     * )
     */
    public function editSellerAction(Request $request, SellerDetails $seller)
    {
        $form = $this->get('form.factory')->createNamed('seller', SellerEditFormType::class, [], [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($this->get('oloy.user.form_handler.seller_edit')->onSuccess($seller->getSellerId(), $form) === true) {
                if ($form->getData()['active']) {
                    $this->get('broadway.command_handling.command_bus')
                        ->dispatch(
                            new ActivateSeller($seller->getSellerId())
                        );
                } else {
                    $this->get('broadway.command_handling.command_bus')
                        ->dispatch(
                            new DeactivateSeller($seller->getSellerId())
                        );
                }

                return $this->view([
                    'sellerId' => $seller->getSellerId()->__toString(),
                ]);
            } else {
                return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }
}
