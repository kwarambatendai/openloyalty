<?php

namespace OpenLoyalty\Bundle\PaginationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class OpenLoyaltyPaginationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('oloy.pagination.pageFieldName', $config['page_field_name']);
        $container->setParameter('oloy.pagination.perPageFieldName', $config['per_page_field_name']);
        $container->setParameter('oloy.pagination.sortFieldName', $config['sort_field_name']);
        $container->setParameter('oloy.pagination.sortDirectionFieldName', $config['sort_direction_field_name']);
        $container->setParameter('oloy.pagination.perPageDefault', $config['per_page_default']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
