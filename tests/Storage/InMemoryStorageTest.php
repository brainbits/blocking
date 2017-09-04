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
use Brainbits\Blocking\Storage\FilesystemStorage;
use Brainbits\Blocking\Exception\DirectoryNotWritableException;
use Brainbits\Blocking\Exception\FileNotWritableException;
use Brainbits\Blocking\Owner\OwnerInterface;
use Brainbits\Blocking\Storage\InMemoryStorage;
use DateTimeImmutable;
use org\bovigo\vfs\vfsStream;
use Brainbits\Blocking\Block;
use Brainbits\Blocking\Identifier\Identifier;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * In memory storage test
 *
 * @author Stephan Wentz <sw@brainbits.net>
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

    public function setUp()
    {
        $this->storage = new InMemoryStorage();

        $this->owner = $this->prophesize(OwnerInterface::class);
        $this->owner->__toString()
            ->willReturn('dummyOwner');
    }

    public function testConstructWithBlock()
    {
        $identifier = new Identifier('test_block');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $storage = new InMemoryStorage($block);

        $result = $storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testWriteSucceedesOnNewBlock()
    {
        $identifier = new Identifier('test_block');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $result = $this->storage->write($block);

        $this->assertTrue($result);
    }

    public function testTouchSucceedesOnExistingBlock()
    {
        $identifier = new Identifier('test_lock');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->touch($block);

        $this->assertTrue($result);
    }

    public function testRemoveReturnsFalseOnNonexistingBlock()
    {
        $identifier = new Identifier('test_unlock');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $result = $this->storage->remove($block);

        $this->assertFalse($result);
    }

    public function testUnblockReturnsTrueOnExistingBlock()
    {
        $identifier = new Identifier('test_unlock');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->remove($block);

        $this->assertTrue($result);
    }

    public function testExistsReturnsFalseOnNonexistingBlock()
    {
        $identifier = new Identifier('test_isblocked');

        $result = $this->storage->exists($identifier);

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingBlock()
    {
        $identifier = new Identifier('test_isblocked');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testGetReturnsNullOnNonexistingFile()
    {
        $identifier = new Identifier('test_isblocked');

        $result = $this->storage->get($identifier);

        $this->assertNull($result);
    }

    public function testGetReturnsBlockOnExistingFile()
    {
        $identifier = new Identifier('test_isblocked');
        $block = new Block($identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->get($identifier);

        $this->assertNotNull($result);
        $this->assertInstanceOf(BlockInterface::class, $result);
    }
}
