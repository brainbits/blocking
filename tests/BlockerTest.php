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

use Brainbits\Blocking\Exception\BlockFailedException;
use Brainbits\Blocking\Storage\StorageInterface;
use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use Brainbits\Blocking\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * Blocker test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BlockerTest extends TestCase
{
    /**
     * @var BlockInterface
     */
    protected $block;

    /**
     * @var IdentifierInterface
     */
    protected $identifier;

    /**
     * @var OwnerInterface
     */
    private $owner;

    public function setUp()
    {
        $this->identifier = $this->prophesize(IdentifierInterface::class);
        $this->identifier->__toString()
            ->willReturn('foo');

        $this->owner = $this->prophesize(OwnerInterface::class);
        $this->owner->__toString()
            ->willReturn('dummyOwner');

        $this->block = $this->prophesize(BlockInterface::class);
        $this->block->getOwner()
            ->willReturn($this->owner->reveal());
    }

    public function testBlockReturnsBlockOnNonexistingBlock()
    {
        $storage = $this->createNonexistingStorage();
        $storage->write(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker($storage->reveal(), $this->owner->reveal(), $this->createInvalidValidator()->reveal());
        $result = $blocker->block($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockReturnsBlockOnExistingAndInvalidBlock()
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();
        $storage->write(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker($storage->reveal(), $this->owner->reveal(), $this->createInvalidValidator()->reveal());
        $result = $blocker->block($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockThrowsExceptionOnExistingAndValidAndNonOwnerBlock()
    {
        $this->expectException(BlockFailedException::class);

        $storage = $this->createExistingStorage();
        $storage->write()
            ->shouldNotBeCalled();

        $owner = $this->prophesize(OwnerInterface::class);
        $owner->__toString()
            ->willReturn('otherOwner');

        $this->block->isOwnedBy($owner->reveal())
            ->willReturn(false);

        $blocker = new Blocker($storage->reveal(), $owner->reveal(), $this->createValidValidator()->reveal());
        $result = $blocker->block($this->identifier->reveal());

        $this->assertNull($result);
    }

    public function testBlockUpdatesBlockOnExistingAndValidAndOwnedBlock()
    {
        $storage = $this->createExistingStorage();
        $storage->touch($this->block)
            ->shouldBeCalled();

        $this->block->isOwnedBy($this->owner->reveal())
            ->willReturn(true);

        $blocker = new Blocker($storage->reveal(), $this->owner->reveal(), $this->createValidValidator()->reveal());
        $result = $blocker->block($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testUnblockReturnsNullOnExistingAndInvalidBlock()
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker($storage->reveal(), $this->owner->reveal(), $this->createInvalidValidator()->reveal());
        $result = $blocker->unblock($this->identifier->reveal());

        $this->assertNull($result);
    }

    public function testUnblockReturnsBlockOnExistingAndValidBlock()
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker($storage->reveal(), $this->owner->reveal(), $this->createValidValidator()->reveal());
        $result = $blocker->unblock($this->identifier->reveal());

        $this->assertSame($this->block->reveal(), $result);
    }

    public function testUnblockReturnsNullOnNonexistingBlock()
    {
        $storage = $this->createNonexistingStorage();
        $storage->remove()
            ->shouldNotBeCalled();

        $blocker = new Blocker($storage->reveal(), $this->owner->reveal(), $this->createInvalidValidator()->reveal());
        $result = $blocker->unblock($this->identifier->reveal());

        $this->assertNull($result);
    }

    public function testIsBlockedReturnsFalseOnExistingAndInvalidBlock()
    {
        $storage = $this->createExistingStorage();
        $storage->remove(Argument::type(BlockInterface::class))
            ->shouldBeCalled();

        $blocker = new Blocker($storage->reveal(), $this->owner->reveal(), $this->createInvalidValidator()->reveal());
        $result = $blocker->isBlocked($this->identifier->reveal());

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingAndValidBlock()
    {
        $blocker = new Blocker($this->createExistingStorage()->reveal(), $this->owner->reveal(), $this->createValidValidator()->reveal());
        $result = $blocker->isBlocked($this->identifier->reveal());

        $this->assertTrue($result);
    }

    public function testIsBlockedReturnsFalseOnNonexistingBlock()
    {
        $blocker = new Blocker($this->createNonexistingStorage()->reveal(), $this->owner->reveal(), $this->createValidValidator()->reveal());
        $result = $blocker->isBlocked($this->identifier->reveal());

        $this->assertFalse($result);
    }

    public function testGetBlockReturnsBlockOnExistingBlock()
    {
        $blocker = new Blocker($this->createExistingStorage()->reveal(), $this->owner->reveal(), $this->createValidValidator()->reveal());
        $result = $blocker->getBlock($this->identifier->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    private function createExistingStorage()
    {
        $storage = $this->prophesize(StorageInterface::class);
        $storage->exists(Argument::type(IdentifierInterface::class))
            ->willReturn(true);
        $storage->get(Argument::type(IdentifierInterface::class))
            ->willReturn($this->block->reveal());

        return $storage;
    }

    private function createNonexistingStorage()
    {
        $storage = $this->prophesize(StorageInterface::class);
        $storage->exists(Argument::type(IdentifierInterface::class))
            ->willReturn(false);

        return $storage;
    }

    private function createInvalidValidator()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate(Argument::type(BlockInterface::class))
            ->willReturn(false);

        return $validator;
    }

    private function createValidValidator()
    {
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate(Argument::type(BlockInterface::class))
            ->willReturn(true);

        return $validator;
    }
}
