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
use Brainbits\Blocking\Exception\DirectoryNotWritableException;
use Brainbits\Blocking\Exception\FileNotWritableException;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Storage\FilesystemStorage;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

use function file_exists;
use function mkdir;

final class FilesystemStorageTest extends TestCase
{
    private FilesystemStorage $storage;

    private string $root;

    private Owner $owner;

    private ClockInterface $clock;

    protected function setUp(): void
    {
        $this->clock = new MockClock();

        vfsStream::setup('blockDir');

        $this->root = vfsStream::url('blockDir');
        $this->storage = new FilesystemStorage($this->clock, $this->root);

        $this->owner = new Owner('dummyOwner');
    }

    public function testWriteSucceedesOnNewFile(): void
    {
        $identifier = new BlockIdentity('test_block');
        $block = new Block($identifier, $this->owner);

        $result = $this->storage->write($block, 10);

        $this->assertTrue($result);
        $this->assertTrue(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testWriteFailsOnNonExistantDirectoryInNonWritableDirectory(): void
    {
        $this->expectException(DirectoryNotWritableException::class);

        vfsStream::setup('nonWritableDir', 0);
        $adapter = new FilesystemStorage(
            $this->clock,
            vfsStream::url('nonWritableDir/blockDir'),
        );

        $identifier = new BlockIdentity('test_lock');
        $block = new Block($identifier, $this->owner);

        $adapter->write($block, 10);
    }

    public function testWriteFailsOnNonWritableDirectory(): void
    {
        $this->expectException(FileNotWritableException::class);

        vfsStream::setup('writableDir');
        mkdir(vfsStream::url('writableDir/nonWritableBlockDir'), 0);

        $adapter = new FilesystemStorage(
            $this->clock,
            vfsStream::url('writableDir/nonWritableBlockDir'),
        );

        $identifier = new BlockIdentity('test_lock');
        $block = new Block($identifier, $this->owner);

        $adapter->write($block, 10);
    }

    public function testTouchSucceedesOnExistingFile(): void
    {
        $identifier = new BlockIdentity('test_lock');
        $block = new Block($identifier, $this->owner);

        $this->storage->write($block, 10);
        $result = $this->storage->touch($block, 10);

        $this->assertTrue($result);
        $this->assertTrue(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testRemoveReturnsFalseOnNonexistingFile(): void
    {
        $identifier = new BlockIdentity('test_unlock');
        $block = new Block($identifier, $this->owner);

        $result = $this->storage->remove($block);

        $this->assertFalse($result);
        $this->assertFalse(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testUnblockReturnsTrueOnExistingFile(): void
    {
        $identifier = new BlockIdentity('test_unlock');
        $block = new Block($identifier, $this->owner);

        $this->storage->write($block, 10);
        $result = $this->storage->remove($block);

        $this->assertTrue($result);
        $this->assertFalse(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testExistsReturnsFalseOnNonexistingFile(): void
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
