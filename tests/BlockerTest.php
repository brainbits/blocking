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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Blocker test
 */
class BlockerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var BlockInterface|ObjectProphecy
     */
    protected $block;

    /**
     * @var IdentityInterface|ObjectProphecy
     */
    protected $identifier;

    /**
     * @var OwnerFactoryInterface|ObjectProphecy
     */
    private $ownerFactory;

    private $owner;

    protected function setUp(): void
    {
        $this->owner = new Owner('bar');

        $this->identifier = $this->prophesize(IdentityInterface::class);
        $this->identifier->__toString()
            ->willReturn('foo');

        $this->ownerFactory = $this->prophesize(OwnerFactoryInterface::class);
        $this->ownerFactory->createOwner()
            ->willReturn($this->owner);

        $this->block = $this->prophesize(BlockInterface::class);
        $this->block->getOwner()
            ->willReturn(new Owner('baz'));
    }

    public function testBlockReturnsBlockOnNonexistingBlock(): void
    {
        $storage = $this->createNonexistingStorage();
        $storage->write(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker(
            $storage->reveal(),
            $this->ownerFactory->reveal(),
            $this->createInvalidValidator()->reveal()
        );
        $result = $blocker->block($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockReturnsBlockOnExistingAndInvalidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();
        $storage->write(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker(
            $storage->reveal(),
            $this->ownerFactory->reveal(),
            $this->createInvalidValidator()->reveal()
        );
        $result = $blocker->block($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockThrowsExceptionOnExistingAndValidAndNonOwnerBlock(): void
    {
        $this->expectException(BlockFailedException::class);

        $storage = $this->createExistingStorage();
        $storage->write()
            ->shouldNotBeCalled();

        $this->block->isOwnedBy($this->owner)
            ->willReturn(false);

        $blocker = new Blocker(
            $storage->reveal(),
            $this->ownerFactory->reveal(),
            $this->createValidValidator()->reveal()
        );
        $result = $blocker->block($this->identifier->reveal());

        $this->assertNull($result);
    }

    public function testBlockUpdatesBlockOnExistingAndValidAndOwnedBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->touch($this->block)
            ->shouldBeCalled();

        $this->block->isOwnedBy($this->owner)
            ->willReturn(true);

        $blocker = new Blocker(
            $storage->reveal(),
            $this->ownerFactory->reveal(),
            $this->createValidValidator()->reveal()
        );
        $result = $blocker->block($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testUnblockReturnsNullOnExistingAndInvalidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker(
            $storage->reveal(),
            $this->ownerFactory->reveal(),
            $this->createInvalidValidator()->reveal()
        );
        $result = $blocker->unblock($this->identifier->reveal());

        $this->assertNull($result);
    }

    public function testUnblockReturnsBlockOnExistingAndValidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker(
            $storage->reveal(),
            $this->ownerFactory->reveal(),
            $this->createValidValidator()->reveal()
        );
        $result = $blocker->unblock($this->identifier->reveal());

        $this->assertSame($this->block->reveal(), $result);
    }

    public function testUnblockReturnsNullOnNonexistingBlock(): void
    {
        $storage = $this->createNonexistingStorage();
        $storage->remove()
            ->shouldNotBeCalled();

        $blocker = new Blocker(
            $storage->reveal(),
            $this->ownerFactory->reveal(),
            $this->createInvalidValidator()->reveal()
        );
        $result = $blocker->unblock($this->identifier->reveal());

        $this->assertNull($result);
    }

    public function testIsBlockedReturnsFalseOnExistingAndInvalidBlock(): void
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker($storage->reveal(), $this->ownerFactory->reveal(), $this->createInvalidValidator()->reveal());
        $result = $blocker->isBlocked($this->identifier->reveal());

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingAndValidBlock(): void
    {
        $blocker = new Blocker(
            $this->createExistingStorage()->reveal(),
            $this->ownerFactory->reveal(),
            $this->createValidValidator()->reveal()
        );
        $result = $blocker->isBlocked($this->identifier->reveal());

        $this->assertTrue($result);
    }

    public function testIsBlockedReturnsFalseOnNonexistingBlock(): void
    {
        $blocker = new Blocker(
            $this->createNonexistingStorage()->reveal(),
            $this->ownerFactory->reveal(),
            $this->createValidValidator()->reveal()
        );
        $result = $blocker->isBlocked($this->identifier->reveal());

        $this->assertFalse($result);
    }

    public function testGetBlockReturnsBlockOnExistingBlock(): void
    {
        $blocker = new Blocker(
            $this->createExistingStorage()->reveal(),
            $this->ownerFactory->reveal(),
            $this->createValidValidator()->reveal()
        );
        $result = $blocker->getBlock($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    /**
     * @return StorageInterface|ObjectProphecy
     */
    private function createExistingStorage()
    {
        $storage = $this->prophesize(StorageInterface::class);
        $storage->exists(Argument::type(IdentityInterface::class))
            ->willReturn(true);
        $storage->get(Argument::type(IdentityInterface::class))
            ->willReturn($this->block->reveal());

        return $storage;
    }

    /**
     * @return StorageInterface|ObjectProphecy
     */
    private function createNonexistingStorage()
    {
        $storage = $this->prophesize(StorageInterface::class);
        $storage->exists(Argument::type(IdentityInterface::class))
            ->willReturn(false);

        return $storage;
    }

    /**
     * @return ValidatorInterface|ObjectProphecy
     */
    private function createInvalidValidator()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate(Argument::type(BlockInterface::class))
            ->willReturn(false);

        return $validator;
    }

    /**
     * @return ValidatorInterface|ObjectProphecy
     */
    private function createValidValidator()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate(Argument::type(BlockInterface::class))
            ->willReturn(true);

        return $validator;
    }
}
