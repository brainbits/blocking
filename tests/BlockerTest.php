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
        $this->identifierMock = $this->getMockBuilder(IdentifierInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ownerMock = $this->getMockBuilder(OwnerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ownerMock
            ->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('dummyOwner'));

        $this->blockMock = $this->getMockBuilder(BlockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->blockMock
            ->expects($this->any())
            ->method('getOwner')
            ->will($this->returnValue($this->ownerMock));
    }

    public function tearDown()
    {
        $this->blockMock      = null;
        $this->ownerMock      = null;
        $this->identifierMock = null;
    }

    private function getExistingAdapterMock()
    {
        $existingAdapterMock = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $existingAdapterMock
            ->expects($this->atLeastOnce())
            ->method('exists')
            ->will($this->returnValue(true));

        $existingAdapterMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnValue($this->blockMock));

        return $existingAdapterMock;
    }

    private function getNonexistingAdapterMock()
    {
        $nonExistingAdapterMock = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $nonExistingAdapterMock
            ->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false));

        return $nonExistingAdapterMock;
    }

    private function getInvalidValidatorMock()
    {
        $invalidValidatorMock = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $invalidValidatorMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(false));

        return $invalidValidatorMock;
    }

    private function getValidValidatorMock()
    {
        $validValidatorMock = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validValidatorMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(true));

        return $validValidatorMock;
    }

    public function testBlockReturnsBlockOnNonexistingBlock()
    {
        $nonExistingAdapterMock = $this->getNonexistingAdapterMock();
        $nonExistingAdapterMock
            ->expects($this->once())
            ->method('write');

        $invalidValidatorMock = $this->getInvalidValidatorMock();

        $blocker = new Blocker($nonExistingAdapterMock, $this->ownerMock, $invalidValidatorMock);
        $result = $blocker->block($this->identifierMock);

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testBlockReturnsBlockOnExistingAndInvalidBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock
            ->expects($this->once())
            ->method('write');

        $blocker = new Blocker($existingAdapterMock, $this->ownerMock, $this->getInvalidValidatorMock());
        $result = $blocker->block($this->identifierMock);

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testBlockThrowsExceptionOnExistingAndValidAndNonOwnerBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock
            ->expects($this->never())
            ->method('write');

        $ownerMock = $this->getMockBuilder(OwnerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ownerMock
            ->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('otherOwner'));

        $blocker = new Blocker($existingAdapterMock, $ownerMock, $this->getValidValidatorMock());
        $result = $blocker->block($this->identifierMock);

        $this->assertNull($result);
    }

    public function testBlockUpdatesBlockOnExistingAndValidAndOwnedBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock
            ->expects($this->once())
            ->method('touch');

        $blocker = new Blocker($existingAdapterMock, $this->ownerMock, $this->getValidValidatorMock());
        $result = $blocker->block($this->identifierMock);

        $this->assertInstanceOf(BlockInterface::class, $result);
    }

    public function testUnblockReturnsFalseOnExistingAndInvalidBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock
            ->expects($this->once())
            ->method('remove');

        $blocker = new Blocker($existingAdapterMock, $this->ownerMock, $this->getInvalidValidatorMock());
        $result = $blocker->unblock($this->identifierMock);

        $this->assertFalse($result);
    }

    public function testUnblockReturnsTrueOnExistingAndValidBlock()
    {
        $existingAdapterMock = $this->getExistingAdapterMock();
        $existingAdapterMock
            ->expects($this->once())
            ->method('remove');

        $blocker = new Blocker($existingAdapterMock, $this->ownerMock, $this->getValidValidatorMock());
        $result = $blocker->unblock($this->identifierMock);

        $this->assertTrue($result);
    }

    public function testUnblockReturnsFalseOnNonexistingBlock()
    {
        $nonExistingAdapterMock = $this->getNonexistingAdapterMock();
        $nonExistingAdapterMock
            ->expects($this->never())
            ->method('remove');

        $blocker = new Blocker($nonExistingAdapterMock, $this->ownerMock, $this->getInvalidValidatorMock());
        $result = $blocker->unblock($this->identifierMock);

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsFalseOnExistingAndInvalidBlock()
    {
        $blocker = new Blocker($this->getExistingAdapterMock(), $this->ownerMock, $this->getInvalidValidatorMock());
        $result = $blocker->isBlocked($this->identifierMock);

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingAndValidBlock()
    {
        $blocker = new Blocker($this->getExistingAdapterMock(), $this->ownerMock, $this->getValidValidatorMock());
        $result = $blocker->isBlocked($this->identifierMock);

        $this->assertTrue($result);
    }

    public function testIsBlockedReturnsFalseOnNonexistingBlock()
    {
        $blocker = new Blocker($this->getNonexistingAdapterMock(), $this->ownerMock, $this->getValidValidatorMock());
        $result = $blocker->isBlocked($this->identifierMock);

        $this->assertFalse($result);
    }

    public function testGetBlockReturnsBlockOnExistingBlock()
    {
        $blocker = new Blocker($this->getExistingAdapterMock(), $this->ownerMock, $this->getValidValidatorMock());
        $result = $blocker->getBlock($this->identifierMock);

        $this->assertInstanceOf('Brainbits\Blocking\BlockInterface', $result);
    }
}
