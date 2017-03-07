<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SegmentBundle;

use OpenLoyalty\Bundle\SegmentBundle\DependencyInjection\CompilerPass\SegmentationEvaluatorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpenLoyaltySegmentBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SegmentationEvaluatorCompilerPass());
    }
}
