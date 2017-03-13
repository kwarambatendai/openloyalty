<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\AuditBundle;

use OpenLoyalty\Bundle\AuditBundle\DependencyInjection\Compiler\AuditableCommandBusCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AuditBundle.
 */
class OpenLoyaltyAuditBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AuditableCommandBusCompilerPass());
    }
}
