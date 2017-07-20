<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\PluginBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @category    DivanteOpenLoyalty
 *
 * @author      Michal Kajszczak <mkajszczak@divante.pl>
 * @copyright   Copyright (C) 2016 Divante Sp. z o.o.
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class SettingsController extends FOSRestController
{
    /**
     * Edit plugins settings.
     *
     * @Route(name="oloy.plugin.edit", path="/plugin")
     * @Method("POST")
     *
     * @ApiDoc(
     *     name="Edit system settings",
     *     section="Plugin",
     *     input={"class" = "OpenLoyaltyPlugin\SalesManagoBundle\Form\Type\SalesManagoFormType", "name" = "salesManago"}
     * )
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function editAction(Request $request)
    {
        $manager = $this->get('oloy.plugin.form.manager');
        $forms = $manager->buildForms();
        $validData = [];
        $errors = [];
        if ($forms) {
            foreach ($forms as $form) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $manager->handleFormSubmit($form);
                    $validData[$form->getName()] = $form->getData();
                } else {
                    $errors[$form->getName()] = $form->getErrors();
                }
            }
            if (!empty($errors)) {
                return $this->view([$validData, $errors], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return $this->view([]);
        }

        return $this->view(['valid_Data' => $validData, 'errors' => $errors]);
    }

    /**
     * Get list of available plugins.
     *
     * @Route(name="oloy.plugin.get", path="/plugin")
     * @Method("GET")
     *
     * @ApiDoc(
     *     name="Get system settings",
     *     section="Plugin"
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAction()
    {
        $objects = $this->getDoctrine()
            ->getRepository('SalesManagoBundle:Config')
            ->findAll();
        $settings = [];
        foreach ($objects as $object) {
            $settings[] = $object->toArray();
        }

        return $this->view([
            'settings' => $settings,
        ], 200);
    }
}
