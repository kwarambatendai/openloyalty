<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SegmentBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SegmentationEvaluatorCompilerPass.
 */
class SegmentationEvaluatorCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('oloy.segment.segmentation_provider')) {
            return;
        }

        $def = $container->getDefinition('oloy.segment.segmentation_provider');

        $tags = $container->findTaggedServiceIds('oloy_segmentation_evaluator');

        foreach ($tags as $id => $args) {
            $def->addMethodCall('addEvaluator', [new Reference($id)]);
        }
    }
}
