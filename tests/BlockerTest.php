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

use Brainbits\Blocking\Adapter\AdapterInterface;
use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use Brainbits\Blocking\Validator\ValidatorInterface;
use PHPUnit_Framework_TestCase as TestCase;
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
    protected $blockMock;

    /**
     * @var AdapterInterface
     */
    protected $existingAdapterMock;

    /**
     * @var AdapterInterface
     */
    protected $nonExistingAdapterMock;

    /**
     * @var ValidatorInterface
     */
    protected $validValidatorMock;

    /**
     * @var ValidatorInterface
     */
    protected $invalidValidatorMock;

    /**
     * @var IdentifierInterface
     */
    protected $identifierMock;

    /**
     * @var OwnerInterface
     */
    private $ownerMock;

    public function setUp()
    {
        $this->identifierMock = $this->prophesize(IdentifierInterface::class);

        $this->ownerMock = $this->prophesize(OwnerInterface::class);
        $this->ownerMock->__toString()->willReturn('dummyOwner');

        $this->blockMock = $this->prophesize(BlockInterface::class);
        $this->blockMock->getOwner()->willReturn($this->ownerMock->reveal());
    }

    private function getExistingAdapterMock()
    {
        $existingAdapterMock = $this->prophesize(AdapterInterface::class);
        $existingAdapterMock->exists(Argument::type(IdentifierInterface::class))->willReturn(true);
        $existingAdapterMock->get(Argument::type(IdentifierInterface::class))->willReturn($this->blockMock->reveal());

        return $existingAdapterMock;
    }

    private function getNonexistingAdapterMock()
    {
        $nonExistingAdapterMock = $this->prophesize(AdapterInterface::class);
        $nonExistingAdapterMock->exists(Argument::type(IdentifierInterface::class))->willReturn(false);

        return $nonExistingAdapterMock;
    }

    private function getInvalidValidatorMock()
    {
        $invalidValidatorMock = $this->prophesize(ValidatorInterface::class);
        $invalidValidatorMock->validate(Argument::type(BlockInterface::class))->willReturn(false);

        return $invalidValidatorMock;
    }

    private function getValidValidatorMock()
    {
        $validValidatorMock = $this->prophesize(ValidatorInterface::class);
        $validValidatorMock->validate(Argument::type(BlockInterface::class))->willReturn(true);

        return $validValidatorMock;
    }

    public function testBlockReturnsBlockOnNonexistingBlock()
    {
        $nonExistingAdapterMock = $this->getNonexistingAdapterMock();
        $nonExistingAdapterMock->write(Argument::type(BlockInterface::class))->shouldBeCalled();

        $blocker = new Blocker($nonExistingAdapterMock->reveal(), $this->ownerMock->reveal(), $this->getInvalidValidatorMock()->reveal());
        $result = $blocker->block($this->identifierMock->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockReturnsBlockOnExistingAndInvalidBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock->remove(Argument::type(BlockInterface::class))->shouldBeCalled();
        $existingAdapterMock->write(Argument::type(BlockInterface::class))->shouldBeCalled();

        $blocker = new Blocker($existingAdapterMock->reveal(), $this->ownerMock->reveal(), $this->getInvalidValidatorMock()->reveal());
        $result = $blocker->block($this->identifierMock->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testBlockThrowsExceptionOnExistingAndValidAndNonOwnerBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock->write()->shouldNotBeCalled();

        $ownerMock = $this->prophesize(OwnerInterface::class);
        $ownerMock->__toString()->willReturn('otherOwner');

        $blocker = new Blocker($existingAdapterMock->reveal(), $ownerMock->reveal(), $this->getValidValidatorMock()->reveal());
        $result = $blocker->block($this->identifierMock->reveal());

        $this->assertNull($result);
    }

    public function testBlockUpdatesBlockOnExistingAndValidAndOwnedBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock->touch(Argument::type(BlockInterface::class))->shouldBeCalled();

        $blocker = new Blocker($existingAdapterMock->reveal(), $this->ownerMock->reveal(), $this->getValidValidatorMock()->reveal());
        $result = $blocker->block($this->identifierMock->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testUnblockReturnsFalseOnExistingAndInvalidBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock->remove(Argument::type(BlockInterface::class))->shouldBeCalled();

        $blocker = new Blocker($existingAdapterMock->reveal(), $this->ownerMock->reveal(), $this->getInvalidValidatorMock()->reveal());
        $result = $blocker->unblock($this->identifierMock->reveal());

        $this->assertFalse($result);
    }

    public function testUnblockReturnsTrueOnExistingAndValidBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock->remove(Argument::type(BlockInterface::class))->shouldBeCalled();

        $blocker = new Blocker($existingAdapterMock->reveal(), $this->ownerMock->reveal(), $this->getValidValidatorMock()->reveal());
        $result = $blocker->unblock($this->identifierMock->reveal());

        $this->assertTrue($result);
    }

    public function testUnblockReturnsFalseOnNonexistingBlock()
    {
        $nonExistingAdapterMock = $this->getNonexistingAdapterMock();
        $nonExistingAdapterMock->remove()->shouldNotBeCalled();

        $blocker = new Blocker($nonExistingAdapterMock->reveal(), $this->ownerMock->reveal(), $this->getInvalidValidatorMock()->reveal());
        $result = $blocker->unblock($this->identifierMock->reveal());

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsFalseOnExistingAndInvalidBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock->remove(Argument::type(BlockInterface::class))->shouldBeCalled();

        $blocker = new Blocker($existingAdapterMock->reveal(), $this->ownerMock->reveal(), $this->getInvalidValidatorMock()->reveal());
        $result = $blocker->isBlocked($this->identifierMock->reveal());

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingAndValidBlock()
    {
        $blocker = new Blocker($this->getExistingAdapterMock()->reveal(), $this->ownerMock->reveal(), $this->getValidValidatorMock()->reveal());
        $result = $blocker->isBlocked($this->identifierMock->reveal());

        $this->assertTrue($result);
    }

    public function testIsBlockedReturnsFalseOnNonexistingBlock()
    {
        $blocker = new Blocker($this->getNonexistingAdapterMock()->reveal(), $this->ownerMock->reveal(), $this->getValidValidatorMock()->reveal());
        $result = $blocker->isBlocked($this->identifierMock->reveal());

        $this->assertFalse($result);
    }

    public function testGetBlockReturnsBlockOnExistingBlock()
    {
        $blocker = new Blocker($this->getExistingAdapterMock()->reveal(), $this->ownerMock->reveal(), $this->getValidValidatorMock()->reveal());
        $result = $blocker->getBlock($this->identifierMock->reveal());

        $this->assertInstanceOf(BlockInterface::class, $result);
    }
}
