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
use Brainbits\Blocking\Owner\SymfonySessionOwnerFactory;
use Brainbits\Blocking\Owner\ValueOwnerFactory;
use Brainbits\Blocking\Storage\FilesystemStorage;
use Brainbits\Blocking\Storage\InMemoryStorage;
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

        $this->assertContainerBuilderHasService('brainbits_blocking.storage', FilesystemStorage::class);
        $this->assertContainerBuilderHasService('brainbits_blocking.owner_factory', SymfonySessionOwnerFactory::class);
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

        $this->assertContainerBuilderHasService('brainbits_blocking.storage', InMemoryStorage::class);
        $this->assertContainerBuilderHasService('brainbits_blocking.owner_factory', ValueOwnerFactory::class);
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

        $this->assertContainerBuilderHasAlias('brainbits_blocking.storage', 'foo');
    }

    public function testPredisStorage(): void
    {
        $this->load([
            'predis' => 'my_predis',
            'storage' => ['driver' => 'predis'],
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

        $this->assertContainerBuilderHasAlias('brainbits_blocking.owner_factory', 'bar');
    }
}
