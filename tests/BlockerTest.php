<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests;

use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Exception\BlockFailedException;
use Brainbits\Blocking\Identity\IdentityInterface;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Owner\OwnerFactoryInterface;
use Brainbits\Blocking\Storage\StorageInterface;
use Brainbits\Blocking\Validator\ValidatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BlockerTest extends TestCase
{
    /**
     * @var BlockInterface|MockObject
     */
    protected $block;

    /**
     * @var IdentityInterface|MockObject
     */
    protected $identifier;

    /**
     * @var OwnerFactoryInterface|MockObject
     */
    private $ownerFactory;

    private $owner;

    protected function setUp(): void
    {
        $this->owner = new Owner('bar');

        $this->identifier = $this->createMock(IdentityInterface::class);
        $this->identifier->expects($this->any())
            ->method('__toString')
            ->willReturn('foo');

        $this->ownerFactory = $this->createMock(OwnerFactoryInterface::class);
        $this->ownerFactory->expects($this->any())
            ->method('createOwner')
            ->willReturn($this->owner);

        $this->block = $this->createMock(BlockInterface::class);
        $this->block->expects($this->any())
            ->method('getOwner')
            ->willReturn(new Owner('baz'));
    }

    public function testBlockReturnsBlockOnNonexistingBlock(): void
    {
        $storage = $this->createNonexistingStorage();
        $storage->expects($this->once())
            ->method('write');

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
            $this->createInvalidValidator()
        );
        $result = $blocker->block($this->identifier);

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockReturnsBlockOnExistingAndInvalidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->expects($this->once())
            ->method('remove');
        $storage->expects($this->once())
            ->method('write');

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
            $this->createInvalidValidator()
        );
        $result = $blocker->block($this->identifier);

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockThrowsExceptionOnExistingAndValidAndNonOwnerBlock(): void
    {
        $this->expectException(BlockFailedException::class);

        $storage = $this->createExistingStorage();
        $storage->expects($this->never())
            ->method('write');

        $this->block->expects($this->once())
            ->method('isOwnedBy')
            ->with($this->owner)
            ->willReturn(false);

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
            $this->createValidValidator()
        );
        $result = $blocker->block($this->identifier);

        $this->assertNull($result);
    }

    public function testBlockUpdatesBlockOnExistingAndValidAndOwnedBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->expects($this->once())
            ->method('touch')
            ->with($this->block);

        $this->block->expects($this->once())
            ->method('isOwnedBy')
            ->with($this->owner)
            ->willReturn(true);

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
            $this->createValidValidator()
        );
        $result = $blocker->block($this->identifier);

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testUnblockReturnsNullOnExistingAndInvalidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->expects($this->once())
            ->method('remove');

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
            $this->createInvalidValidator()
        );
        $result = $blocker->unblock($this->identifier);

        $this->assertNull($result);
    }

    public function testUnblockReturnsBlockOnExistingAndValidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->expects($this->once())
            ->method('remove');

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
            $this->createValidValidator()
        );
        $result = $blocker->unblock($this->identifier);

        $this->assertSame($this->block, $result);
    }

    public function testUnblockReturnsNullOnNonexistingBlock(): void
    {
        $storage = $this->createNonexistingStorage();
        $storage->expects($this->never())
            ->method('remove');

        $blocker = new Blocker(
            $storage,
            $this->ownerFactory,
            $this->createInvalidValidator()
        );
        $result = $blocker->unblock($this->identifier);

        $this->assertNull($result);
    }

    public function testIsBlockedReturnsFalseOnExistingAndInvalidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->expects($this->once())
            ->method('remove');

        $blocker = new Blocker($storage, $this->ownerFactory, $this->createInvalidValidator());
        $result = $blocker->isBlocked($this->identifier);

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingAndValidBlock(): void
    {
        $blocker = new Blocker(
            $this->createExistingStorage(),
            $this->ownerFactory,
            $this->createValidValidator()
        );
        $result = $blocker->isBlocked($this->identifier);

        $this->assertTrue($result);
    }

    public function testIsBlockedReturnsFalseOnNonexistingBlock(): void
    {
        $blocker = new Blocker(
            $this->createNonexistingStorage(),
            $this->ownerFactory,
            $this->createValidValidator()
        );
        $result = $blocker->isBlocked($this->identifier);

        $this->assertFalse($result);
    }

    public function testGetBlockReturnsBlockOnExistingBlock(): void
    {
        $blocker = new Blocker(
            $this->createExistingStorage(),
            $this->ownerFactory,
            $this->createValidValidator()
        );
        $result = $blocker->getBlock($this->identifier);

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    /**
     * @return StorageInterface|MockObject
     */
    private function createExistingStorage()
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

    /**
     * @return StorageInterface|MockObject
     */
    private function createNonexistingStorage()
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

    /**
     * @return ValidatorInterface|MockObject
     */
    private function createInvalidValidator()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->any())
            ->method('validate')
            ->willReturn(false);

        return $validator;
    }

    /**
     * @return ValidatorInterface|MockObject
     */
    private function createValidValidator()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->any())
            ->method('validate')
            ->willReturn(true);

        return $validator;
    }
}
