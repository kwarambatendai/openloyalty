<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EmailSettingsBundle;

use OpenLoyalty\Bundle\EmailSettingsBundle\DependencyInjection\Compiler\OverrideOloySwiftmailerMailer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenLoyaltyEmailSettingsBundle.
 */
class OpenLoyaltyEmailSettingsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideOloySwiftmailerMailer());
    }
}
