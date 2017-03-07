<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PaginationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_loyalty_pagination');
        $rootNode->children()->scalarNode('page_field_name')->isRequired()->end();
        $rootNode->children()->scalarNode('per_page_field_name')->isRequired()->end();
        $rootNode->children()->scalarNode('sort_field_name')->isRequired()->end();
        $rootNode->children()->scalarNode('sort_direction_field_name')->isRequired()->end();
        $rootNode->children()->scalarNode('per_page_default')->isRequired()->end();

        return $treeBuilder;
    }
}
