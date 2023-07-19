<?php

declare(strict_types=1);

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use function json_encode;

/**
 * Blocking configuration.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('brainbits_blocking');
        $rootNode = $treeBuilder->getRootNode();

        $storageDrivers = ['filesystem', 'predis', 'in_memory', 'custom'];
        $ownerFactoryDrivers = ['symfony_session', 'symfony_token', 'value', 'custom'];
        $validatorDrivers = ['expired', 'always_invalidate', 'custom'];

        $rootNode
            ->beforeNormalization()
                ->ifTrue(static function ($v) {
                    if (($v['storage']['driver'] ?? '') !== 'predis') {
                        return false;
                    }

                    return ($v['predis'] ?? '') === '';
                })
                ->thenInvalid(
                    'A predis alias has to be set for the predis storage driver.',
                )
            ->end()
            ->children()
                ->integerNode('block_interval')->defaultValue(30)->end()
                ->scalarNode('clock')->end()
                ->scalarNode('predis')->end()
                ->arrayNode('storage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')
                            ->validate()
                                ->ifNotInArray($storageDrivers)
                                ->thenInvalid(
                                    'The storage driver %s is not supported. Please choose one of ' .
                                    json_encode($storageDrivers),
                                )
                            ->end()
                            ->defaultValue('filesystem')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('service')->end()
                        ->scalarNode('storage_dir')->defaultValue('%kernel.cache_dir%/blocking/')->end()
                        ->scalarNode('prefix')->defaultValue('block')->end()
                    ->end()
                ->end()
                ->arrayNode('owner_factory')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')
                            ->validate()
                                ->ifNotInArray($ownerFactoryDrivers)
                                ->thenInvalid(
                                    'The owner_factory driver %s is not supported. Please choose one of ' .
                                    json_encode($ownerFactoryDrivers),
                                )
                            ->end()
                            ->defaultValue('symfony_session')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('service')->end()
                        ->scalarNode('value')->end()
                    ->end()
                ->end()
                ->arrayNode('validator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')
                            ->validate()
                                ->ifNotInArray($validatorDrivers)
                                ->thenInvalid(
                                    'The validator driver %s is not supported. Please choose one of ' .
                                    json_encode($validatorDrivers),
                                )
                            ->end()
                            ->defaultValue('expired')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('service')->end()
                        ->integerNode('expiration_time')->defaultValue(300)->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(static function ($v) {
                    return $v['storage']['driver'] === 'custom' && empty($v['storage']['service']);
                })
                ->thenInvalid('You need to specify your own storage service when using the "custom" storage driver.')
            ->end()
            ->validate()
                ->ifTrue(static function ($v) {
                    return $v['owner_factory']['driver'] === 'custom' && empty($v['owner_factory']['service']);
                })
                ->thenInvalid(
                    'You need to specify your own owner_factory service when using the "custom" owner_factory driver.',
                )
            ->end()
            ->validate()
                ->ifTrue(static function ($v) {
                    return $v['validator']['driver'] === 'custom' && empty($v['validator']['service']);
                })
                ->thenInvalid(
                    'You need to specify your own validator service when using the "custom" validator driver.',
                )
            ->end();

        return $treeBuilder;
    }
}
