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

namespace Brainbits\Blocking\Tests;

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Owner\OwnerFactoryInterface;
use Brainbits\Blocking\Owner\ValueOwnerFactory;
use Brainbits\Blocking\Storage\StorageInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BlockerTest extends TestCase
{
    private Block $block;

    private BlockIdentity $identifier;

    private OwnerFactoryInterface $ownerFactory;

    private Owner $owner;

    protected function setUp(): void
    {
        $this->owner = new Owner('bar');

        $this->identifier = new BlockIdentity('foo');

        $this->ownerFactory = new ValueOwnerFactory('bar');

        $this->block = new Block($this->identifier, new Owner('baz'));
    }

    public function testBlockReturnsBlockOnNonexistingBlock(): void
    {
        $storage = $this->createNonexistingStorage();
        $storage->expects($this->once())
            ->method('write');

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
        );
        $result = $blocker->block($this->identifier);

        $this->assertInstanceOf(Block::class, $result);
    }

    public function testUnblockReturnsNullOnNonexistingBlock(): void
    {
        $storage = $this->createNonexistingStorage();
        $storage->expects($this->never())
            ->method('remove');

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
        );
        $result = $blocker->unblock($this->identifier);

        $this->assertNull($result);
    }

    public function testIsBlockedReturnsFalseOnNonexistingBlock(): void
    {
        $blocker = new Blocker(
            $this->createNonexistingStorage(),
            $this->ownerFactory,
        );
        $result = $blocker->isBlocked($this->identifier);

        $this->assertFalse($result);
    }

    public function testGetBlockReturnsBlockOnExistingBlock(): void
    {
        $blocker = new Blocker(
            $this->createExistingStorage(),
            $this->ownerFactory,
        );
        $result = $blocker->getBlock($this->identifier);

        $this->assertInstanceOf(Block::class, $result);
    }

    private function createExistingStorage(): StorageInterface|MockObject
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->any())
            ->method('exists')
            ->willReturn(true);
        $storage->expects($this->any())
            ->method('get')
            ->willReturn($this->block);

        return $storage;
    }

    private function createNonexistingStorage(): StorageInterface|MockObject
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->any())
            ->method('exists')
            ->willReturn(false);
        $storage->expects($this->any())
            ->method('get')
            ->willReturn(null);

        return $storage;
    }
}
