<?php

declare(strict_types=1);

/*
 * This file is part of the brainbits blocking bundle package.
 *
 * (c) brainbits GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Bundle\Tests\DependencyInjection;

use Brainbits\Blocking\Bundle\DependencyInjection\BrainbitsBlockingExtension;
use Brainbits\Blocking\Owner\OwnerFactoryInterface;
use Brainbits\Blocking\Owner\SymfonySessionOwnerFactory;
use Brainbits\Blocking\Owner\ValueOwnerFactory;
use Brainbits\Blocking\Storage\FilesystemStorage;
use Brainbits\Blocking\Storage\InMemoryStorage;
use Brainbits\Blocking\Storage\StorageInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class BrainbitsBlockingExtensionTest extends AbstractExtensionTestCase
{
    /** @return Extension[] */
    protected function getContainerExtensions(): array
    {
        return [new BrainbitsBlockingExtension()];
    }

    public function testContainerHasDefaultParameters(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(FilesystemStorage::class);
        $this->assertContainerBuilderHasAlias(StorageInterface::class, FilesystemStorage::class);
        $this->assertContainerBuilderHasService(OwnerFactoryInterface::class, SymfonySessionOwnerFactory::class);
        $this->assertContainerBuilderHasParameter('brainbits_blocking.interval', 30);
    }

    public function testContainerHasCustomParameters(): void
    {
        $this->load([
            'storage' => ['driver' => 'in_memory'],
            'owner_factory' => [
                'driver' => 'value',
                'value' => 'xx',
            ],
            'block_interval' => 9,
        ]);

        $this->assertContainerBuilderHasService(InMemoryStorage::class);
        $this->assertContainerBuilderHasAlias(StorageInterface::class, InMemoryStorage::class);
        $this->assertContainerBuilderHasService(OwnerFactoryInterface::class, ValueOwnerFactory::class);
        $this->assertContainerBuilderHasParameter('brainbits_blocking.interval', 9);
    }

    public function testCustomStorageService(): void
    {
        $this->load([
            'storage' => [
                'driver' => 'custom',
                'service' => 'foo',
            ],
        ]);

        $this->assertContainerBuilderHasAlias(StorageInterface::class, 'foo');
    }

    public function testPredisStorage(): void
    {
        $this->load([
            'storage' => [
                'driver' => 'predis',
                'predis' => 'my_predis',
            ],
        ]);

        $this->assertContainerBuilderHasAlias('brainbits_blocking.predis', 'my_predis');
    }

    public function testCustomOwnerService(): void
    {
        $this->load([
            'owner_factory' => [
                'driver' => 'custom',
                'service' => 'bar',
            ],
        ]);

        $this->assertContainerBuilderHasAlias(OwnerFactoryInterface::class, 'bar');
    }
}
