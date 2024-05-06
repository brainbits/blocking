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

use Brainbits\Blocking\Owner\OwnerFactoryInterface;
use Brainbits\Blocking\Storage\StorageInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use function assert;
use function sprintf;

class BrainbitsBlockingExtension extends Extension
{
    /** @param array<int, mixed> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $yamlLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $yamlLoader->load('services.yaml');
        $configuration = $this->getConfiguration($configs, $container);

        assert($configuration instanceof Configuration);

        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['storage']['predis'])) {
            $container->setAlias('brainbits_blocking.predis', $config['storage']['predis']);
        }

        if (isset($config['clock'])) {
            $container->setAlias('brainbits_blocking.clock', $config['clock']);
        }

        $container->setParameter('brainbits_blocking.interval', $config['block_interval']);

        if (isset($config['storage']['storage_dir'])) {
            $container->setParameter(
                'brainbits_blocking.storage.storage_dir',
                $config['storage']['storage_dir'],
            );
        }

        if (isset($config['storage']['prefix'])) {
            $container->setParameter(
                'brainbits_blocking.storage.prefix',
                $config['storage']['prefix'],
            );
        }

        if (isset($config['owner_factory']['value'])) {
            $container->setParameter(
                'brainbits_blocking.owner_factory.value',
                $config['owner_factory']['value'],
            );
        }

        if ($config['storage']['driver'] !== 'custom') {
            $yamlLoader->load(sprintf('storage/%s.yaml', $config['storage']['driver']));
        } else {
            $container->setAlias(StorageInterface::class, $config['storage']['service']);
        }

        if ($config['owner_factory']['driver'] !== 'custom') {
            $yamlLoader->load(sprintf('owner_factory/%s.yaml', $config['owner_factory']['driver']));
        } else {
            $container->setAlias(OwnerFactoryInterface::class, $config['owner_factory']['service']);
        }
    }
}
