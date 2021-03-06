<?php

namespace Nmotion\NmotionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nmotion_nmotion');
        
        $rootNode
            ->children()
                ->arrayNode('facebook')
                    ->children()
                        ->scalarNode('app_id')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('file')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('upload')
                    ->children()
                        ->scalarNode('root_dir')->cannotBeEmpty()->end()
                        ->scalarNode('root_web')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
