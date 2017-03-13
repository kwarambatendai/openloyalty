<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EmailBundle\DependencyInjection;

use OpenLoyalty\Bundle\EmailBundle\Exception\InvalidEmailConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Class OpenLoyaltyEmailExtension.
 */
class OpenLoyaltyEmailExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $this->validateEmailsParameter($container);
    }

    /**
     * Configure emails parameter.
     *
     * @param ContainerBuilder $container
     */
    protected function validateEmailsParameter(ContainerBuilder $container)
    {
        try {
            $emails = $container->getParameter('oloy.emails');

            // validate structure
            foreach ($emails as $email) {
                if (!array_key_exists('template', $email) ||
                    !array_key_exists('subject', $email) ||
                    !array_key_exists('variables', $email)
                ) {
                    throw new InvalidEmailConfigurationException();
                }
            }
        } catch (InvalidArgumentException $e) {
            $emails = [];
        }

        $container->setParameter('oloy.email.emails', $emails);
    }
}
