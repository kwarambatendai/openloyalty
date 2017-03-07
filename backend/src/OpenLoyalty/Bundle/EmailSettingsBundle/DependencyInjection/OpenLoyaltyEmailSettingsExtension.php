<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 31.01.17 08:51
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Bundle\EmailSettingsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class OpenLoyaltyEmailSettingsExtension.
 */
class OpenLoyaltyEmailSettingsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('domain.yml');
        $loader->load('services.yml');
    }
}
