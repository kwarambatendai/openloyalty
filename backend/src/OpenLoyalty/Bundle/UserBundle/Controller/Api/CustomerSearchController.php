<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\UserBundle\Form\Type\CustomerSearchFormType;
use OpenLoyalty\Bundle\UserBundle\Model\SearchCustomer;
use OpenLoyalty\Domain\Customer\Exception\ToManyResultsException;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomerSearchController.
 */
class CustomerSearchController extends FOSRestController
{
    /**
     * This method should be used to search customers.
     *
     * @Route(name="oloy.user.search", path="/pos/search/customer")
     * @Method("POST")
     * @Security("is_granted('SEARCH_CUSTOMER')")
     * @ApiDoc(
     *     name="Search customer",
     *     section="Customer",
     *     input={"class" = "OpenLoyalty\Bundle\UserBundle\Form\Type\CustomerSearchFormType", "name" = "search"},
     *     statusCodes={
     *       200="Returned when successful",
     *       400="Returned when form contains errors or there are to many results and search query should be more specific",
     *     }
     * )
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function findAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('search', CustomerSearchFormType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var CustomerDetailsRepository $repo */
            $repo = $this->get('oloy.user.read_model.repository.customer_details');
            /** @var SearchCustomer $data */
            $data = $form->getData();
            try {
                $customers = $repo->findOneByCriteria($data->toCriteriaArray(), $this->container->getParameter('oloy.user.customerSearchMaxResults'));
            } catch (ToManyResultsException $e) {
                return $this->view(['error' => 'to many results'], 400);
            }

            return $this->view(['customers' => $customers]);
        }

        return $this->view($form->getErrors(), 400);
    }
}
