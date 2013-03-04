<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking;

use Brainbits\Blocking\Identifier\IdentifierInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Block test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BlockTest extends TestCase
{
    public function setUp()
    {
        $this->identifierMock = $this->getMockBuilder('Brainbits\Blocking\Identifier\IdentifierInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->ownerMock = $this->getMockBuilder('Brainbits\Blocking\Owner\OwnerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->ownerMock
            ->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('dummyOwner'));
    }

    public function tearDown()
    {
        $this->ownerMock = null;
        $this->identifier = null;
    }

    public function testConstruct()
    {
        $block = new Block($this->identifierMock, $this->ownerMock, new \DateTime());
    }

    public function testGetIdentifierReturnsCorrectValue()
    {
        $block = new Block($this->identifierMock, $this->ownerMock, new \DateTime());

        $this->assertSame($this->identifierMock, $block->getIdentifier());
    }

    public function testGetOwnerReturnsCorrectValue()
    {
        $block = new Block($this->identifierMock, $this->ownerMock, new \DateTime());

        $this->assertSame($this->ownerMock, $block->getOwner());
    }

    public function testGetCreatedAtReturnsCorrectValue()
    {
        $createdAt = new \DateTime();

        $block = new Block($this->identifierMock, $this->ownerMock, $createdAt);
        $result = $block->getCreatedAt();

        $this->assertInstanceOf('\DateTime', $result);
        $this->assertSame($createdAt, $result);
    }

    public function testGetUpdatedAtReturnsCreatedAtValueAfterInstanciation()
    {
        $createdAt = new \DateTime();

        $block = new Block($this->identifierMock, $this->ownerMock, $createdAt);
        $result = $block->getUpdatedAt();

        $this->assertInstanceOf('\DateTime', $result);
        $this->assertSame($createdAt, $result);
    }

    public function testSetUpdatedAtUpdatesValue()
    {
        $createdAt = new \DateTime();

        $block = new Block($this->identifierMock, $this->ownerMock, $createdAt);

        $updatedAt = new \DateTime();
        $block->setUpdatedAt($updatedAt);
        $result = $block->getUpdatedAt();

        $this->assertInstanceOf('\DateTime', $result);
        $this->assertSame($updatedAt, $result);
    }
}