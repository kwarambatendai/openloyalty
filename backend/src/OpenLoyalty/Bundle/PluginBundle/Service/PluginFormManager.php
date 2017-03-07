<?php

namespace OpenLoyalty\Bundle\PluginBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;

/**
 * @category    DivanteOpenLoyalty
 *
 * @author      Michal Kajszczak <mkajszczak@divante.pl>
 * @copyright   Copyright (C) 2016 Divante Sp. z o.o.
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class PluginFormManager
{
    /**
     * @var
     */
    protected $pluginModules;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * PluginFormManager constructor.
     *
     * @param array              $kernelModules
     * @param ContainerInterface $container
     */
    public function __construct($kernelModules, ContainerInterface $container)
    {
        foreach ($kernelModules as $module => $namespace) {
            if (strpos($namespace, 'OpenLoyaltyPlugin\\') !== false) {
                $this->pluginModules[] = strtolower(str_replace('Bundle', '', $module));
            }
        }

        $this->container = $container;
    }

    /**
     * @return array
     */
    public function buildForms()
    {
        $forms = [];
        foreach ($this->pluginModules as $module) {
            $formBuilder = $this->container->get('ol.'.$module.'.formbuilder');
            $forms[] = $formBuilder->createForm();
        }

        return $forms;
    }

    /**
     * @param Form $formData
     */
    public function handleFormSubmit(Form $formData)
    {
        $handler = $this->container->get('ol.'.$formData->getName().'.formhandler');
        $handler->handleForm($formData);
    }
}
