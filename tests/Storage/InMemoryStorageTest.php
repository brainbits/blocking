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

namespace Brainbits\Blocking\Tests\Storage;

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Storage\InMemoryStorage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

final class InMemoryStorageTest extends TestCase
{
    private InMemoryStorage $storage;

    private Owner $owner;

    private ClockInterface $clock;

    protected function setUp(): void
    {
        $this->clock = new MockClock();

        $this->storage = new InMemoryStorage($this->clock);

        $this->owner = new Owner('dummyOwner');
    }

    public function testConstructWithAdd(): void
    {
        $identifier = new BlockIdentity('test_block');
        $block = new Block($identifier, $this->owner);

        $storage = new InMemoryStorage($this->clock);
        $storage->addBlock($block, 60, new DateTimeImmutable());

        $result = $storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testWriteSucceedesOnNewBlock(): void
    {
        $identifier = new BlockIdentity('test_block');
        $block = new Block($identifier, $this->owner);

        $result = $this->storage->write($block, 10);

        $this->assertTrue($result);
    }

    public function testTouchSucceedesOnExistingBlock(): void
    {
        $identifier = new BlockIdentity('test_lock');
        $block = new Block($identifier, $this->owner);

        $this->storage->write($block, 10);
        $result = $this->storage->touch($block, 10);

        $this->assertTrue($result);
    }

    public function testRemoveReturnsFalseOnNonexistingBlock(): void
    {
        $identifier = new BlockIdentity('test_unlock');
        $block = new Block($identifier, $this->owner);

        $result = $this->storage->remove($block);

        $this->assertFalse($result);
    }

    public function testUnblockReturnsTrueOnExistingBlock(): void
    {
        $identifier = new BlockIdentity('test_unlock');
        $block = new Block($identifier, $this->owner);

        $this->storage->write($block, 10);
        $result = $this->storage->remove($block);

        $this->assertTrue($result);
    }

    public function testExistsReturnsFalseOnNonexistingBlock(): void
    {
        $identifier = new BlockIdentity('test_isblocked');

        $result = $this->storage->exists($identifier);

        $this->assertFalse($result);
    }

    public function testIsBlockedReturnsTrueOnExistingBlock(): void
    {
        $identifier = new BlockIdentity('test_isblocked');
        $block = new Block($identifier, $this->owner);

        $this->storage->write($block, 10);
        $result = $this->storage->exists($identifier);

        $this->assertTrue($result);
    }

    public function testGetReturnsNullOnNonexistingFile(): void
    {
        $identifier = new BlockIdentity('test_isblocked');

        $result = $this->storage->get($identifier);

        $this->assertNull($result);
    }

    public function testGetReturnsBlockOnExistingFile(): void
    {
        $identifier = new BlockIdentity('test_isblocked');
        $block = new Block($identifier, $this->owner);

        $this->storage->write($block, 10);
        $result = $this->storage->get($identifier);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Block::class, $result);
    }
}
