<?php

namespace OpenLoyalty\Bundle\AuditBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AuditableCommandBusCompilerPass.
 */
class AuditableCommandBusCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('broadway.event_dispatcher')->setLazy(true);
    }
}
