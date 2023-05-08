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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InMemoryStorageTest extends TestCase
{
    /**
     * @var InMemoryStorage
     */
    private $storage;

    /**
     * @var OwnerInterface|MockObject
     */
    private $owner;

    protected function setUp(): void
    {
        $this->storage = new InMemoryStorage();

        $this->owner = $this->createMock(OwnerInterface::class);
        $this->owner->expects($this->any())
            ->method('__toString')
            ->willReturn('dummyOwner');
    }

    public function testConstructWithBlock(): void
    {
        $identifier = new Identity('test_block');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $storage = new InMemoryStorage($block);

        $result = $storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testWriteSucceedesOnNewBlock(): void
    {
        $identifier = new Identity('test_block');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $result = $this->storage->write($block);

        $this->assertTrue($result);
    }

    public function testTouchSucceedesOnExistingBlock(): void
    {
        $identifier = new Identity('test_lock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->touch($block);

        $this->assertTrue($result);
    }

    public function testRemoveReturnsFalseOnNonexistingBlock(): void
    {
        $identifier = new Identity('test_unlock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $result = $this->storage->remove($block);

        $this->assertFalse($result);
    }

    public function testUnblockReturnsTrueOnExistingBlock(): void
    {
        $identifier = new Identity('test_unlock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->remove($block);

        $this->assertTrue($result);
    }

    public function testExistsReturnsFalseOnNonexistingBlock(): void
    {
        $identifier = new Identity('test_isblocked');

        $result = $this->storage->exists($identifier);

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingBlock(): void
    {
        $identifier = new Identity('test_isblocked');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testGetReturnsNullOnNonexistingFile(): void
    {
        $identifier = new Identity('test_isblocked');

        $result = $this->storage->get($identifier);

        $this->assertNull($result);
    }

    public function testGetReturnsBlockOnExistingFile(): void
    {
        $identifier = new Identity('test_isblocked');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->get($identifier);

        $this->assertNotNull($result);
        $this->assertInstanceOf(BlockInterface::class, $result);
    }
}
