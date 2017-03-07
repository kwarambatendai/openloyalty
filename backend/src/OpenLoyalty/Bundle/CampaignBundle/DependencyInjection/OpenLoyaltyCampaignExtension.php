<?php

namespace OpenLoyalty\Bundle\CampaignBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class OpenLoyaltyCampaignExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $value = $config['photos_adapter_env'];
        if (!$value || !getenv($value)) {
            $value = $config['photos_adapter'];
            if (!$value) {
                throw new \LogicException('photos_adapter_env or photos_adapter must be configured');
            }
        } else {
            $value = getenv($value);
        }
        $container->setParameter('oloy.campaign.photos_adapter', $value);
        $container->setParameter('oloy.campaign.photos_min_width', $config['photos_min_width']);
        $container->setParameter('oloy.campaign.photos_min_height', $config['photos_min_height']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('domain.yml');
        $loader->load('voters.yml');
    }
}
