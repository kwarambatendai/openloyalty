<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle;

use OpenLoyalty\Bundle\EarningRuleBundle\DependencyInjection\Compiler\EarningRuleAlgorithmFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpenLoyaltyEarningRuleBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new EarningRuleAlgorithmFactoryPass());
    }
}
