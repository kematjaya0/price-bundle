<?php

namespace Kematjaya\PriceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('price');
        $rootNode = $treeBuilder->getRootNode();

        $this->addCurrencyConfiguration($rootNode->children());

        return $treeBuilder;
    }

    protected function addCurrencyConfiguration(NodeBuilder $node):void
    {
        $node
            ->arrayNode('currency')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('code')->defaultValue('IDR')->end()
            ->integerNode('cent_limit')->defaultValue(0)->end()
            ->scalarNode('cent_point')->defaultValue('.')->end()
            ->scalarNode('thousand_point')->defaultValue(',')->end()
            ->booleanNode("allow_negative")->defaultValue(true)->end()
            ->end()
            ->end()
            ->end();
    }
}
