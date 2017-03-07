<?php
/*
 * This file is part of the "open-loyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 14.02.17 14:25
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
