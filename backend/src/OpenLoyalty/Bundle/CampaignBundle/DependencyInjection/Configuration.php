<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\CampaignBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_loyalty_campaign');
        $rootNode->children()->scalarNode('photos_adapter')->defaultNull()->end();
        $rootNode->children()->scalarNode('photos_adapter_env')->defaultNull()->end();
        $rootNode->children()->scalarNode('photos_min_width')->isRequired()->end();
        $rootNode->children()->scalarNode('photos_min_height')->isRequired()->end();

        return $treeBuilder;
    }
}
