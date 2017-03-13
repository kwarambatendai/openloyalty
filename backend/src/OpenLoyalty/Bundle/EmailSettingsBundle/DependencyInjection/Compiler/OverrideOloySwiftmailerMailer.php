<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EmailSettingsBundle\DependencyInjection\Compiler;

use OpenLoyalty\Bundle\EmailSettingsBundle\Mailer\OloySwiftmailerMailer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class OverrideOloySwiftmailerMailer.
 */
class OverrideOloySwiftmailerMailer implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('oloy.swiftmailer')) {
            $definition = $container->getDefinition('oloy.swiftmailer');
            $definition->setClass(OloySwiftmailerMailer::class);
            $definition->addArgument(new Reference('oloy.email.read_model.repository'));
            $definition->addArgument(new Reference('twig'));
        }
    }
}
