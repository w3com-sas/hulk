<?php

namespace W3com\HulkBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('w3com_hulk');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('json_display')
                    ->children()
                        ->scalarNode('url_files')
                        ->defaultValue('/Display/')->end()
                    ->end()
                ->end()
                ->scalarNode('max_result_returned')
                    ->defaultValue('1000')
                ->end()
                ->scalarNode('hulk_list_caching_display')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
