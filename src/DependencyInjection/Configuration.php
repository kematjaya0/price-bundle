<?php

/**
 * This file is part of the kematjaya/price-bundle.
 */

declare(strict_types=1);

namespace Kematjaya\PriceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('price');
        $rootNode = $treeBuilder->getRootNode();

        $this->addCurrencyConfiguration($rootNode->children());

        return $treeBuilder;
    }

    protected function addCurrencyConfiguration(NodeBuilder $node): void
    {
        $node
            ->arrayNode('currency')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('code')->defaultValue('IDR')->end()
                    ->integerNode('cent_limit')->defaultValue(0)->end()
                    ->scalarNode('cent_point')->defaultValue('.')->end()
                    ->scalarNode('thousand_point')->defaultValue(',')->end()
                    ->booleanNode('allow_negative')->defaultValue(true)->end()
                    ->arrayNode('cent_limits')
                        ->info(
                            'Per-currency-code override of cent_limit, e.g. {USD: 2}. '
                            . 'Currencies not listed fall back to cent_limit.'
                        )
                        ->useAttributeAsKey('name')
                        ->scalarPrototype()->end()
                        ->defaultValue([])
                    ->end()
                ->end()
            ->end();
    }
}