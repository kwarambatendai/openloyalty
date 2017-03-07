<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EarningRuleAlgorithmFactoryPass.
 */
class EarningRuleAlgorithmFactoryPass implements CompilerPassInterface
{
    const SERVICE_ID = 'oloy.earning_rule.algorithm_factory';
    const TAG_NAME = 'oloy.earning_rule.algorithm';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::SERVICE_ID)) {
            return;
        }

        $definition = $container->getDefinition(self::SERVICE_ID);
        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addAlgorithm',
                    [
                        new Reference($id),
                        $attributes['alias'],
                    ]
                );
            }
        }
    }
}
