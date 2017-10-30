<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests\Storage;

use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use Brainbits\Blocking\Storage\InMemoryStorage;
use DateTimeImmutable;
use Brainbits\Blocking\Block;
use Brainbits\Blocking\Identity\Identity;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * In memory storage test
 */
class InMemoryStorageTest extends TestCase
{
    /**
     * @var InMemoryStorage
     */
    private $storage;

    /**
     * @var OwnerInterface|ObjectProphecy
     */
    private $owner;

    protected function setUp()
    {
        $this->storage = new InMemoryStorage();

        $this->owner = $this->prophesize(OwnerInterface::class);
        $this->owner->__toString()
            ->willReturn('dummyOwner');
    }

    public function testConstructWithBlock()
    {
        $identifier = new Identity('test_block');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $storage = new InMemoryStorage($block);

        $result = $storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testWriteSucceedesOnNewBlock()
    {
        $identifier = new Identity('test_block');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $result = $this->storage->write($block);

        $this->assertTrue($result);
    }

    public function testTouchSucceedesOnExistingBlock()
    {
        $identifier = new Identity('test_lock');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->touch($block);

        $this->assertTrue($result);
    }

    public function testRemoveReturnsFalseOnNonexistingBlock()
    {
        $identifier = new Identity('test_unlock');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $result = $this->storage->remove($block);

        $this->assertFalse($result);
    }

    public function testUnblockReturnsTrueOnExistingBlock()
    {
        $identifier = new Identity('test_unlock');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->remove($block);

        $this->assertTrue($result);
    }

    public function testExistsReturnsFalseOnNonexistingBlock()
    {
        $identifier = new Identity('test_isblocked');

        $result = $this->storage->exists($identifier);

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingBlock()
    {
        $identifier = new Identity('test_isblocked');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testGetReturnsNullOnNonexistingFile()
    {
        $identifier = new Identity('test_isblocked');

        $result = $this->storage->get($identifier);

        $this->assertNull($result);
    }

    public function testGetReturnsBlockOnExistingFile()
    {
        $identifier = new Identity('test_isblocked');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->get($identifier);

        $this->assertNotNull($result);
        $this->assertInstanceOf(BlockInterface::class, $result);
    }
}
