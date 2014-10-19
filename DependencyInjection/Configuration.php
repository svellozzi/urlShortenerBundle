<?php

namespace Vellozzi\UrlShortenerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $allowedCharForTag = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
        $infoTag = "size of the wanted tag when the url is shortened";
        $infoAllowedChar = "string which contains the allowed characters used when the url is shortened";
        $infoBundle = "Configuration for the VellozziUrlShortenerBundle";
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vellozzi_url_shortener');
        
        $rootNode
            ->addDefaultsIfNotSet()
            ->info($infoBundle)
            ->children()
                ->integerNode('tag_size')
                    ->defaultValue(5)
                    ->min(1)
                    ->max(50)
                    ->info($infoTag)
                ->end()
                ->scalarNode('allowedChar')
                    ->defaultValue($allowedCharForTag)
                    ->info($infoAllowedChar)
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('item_per_page')
                            ->defaultValue(50)
                            ->min(1)
                            ->max(PHP_INT_MAX)
                        ->end()
                    ->end() 
                ->end()
            ->end();

        return $treeBuilder;
    }
}
