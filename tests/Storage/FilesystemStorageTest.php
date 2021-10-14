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
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Storage\FilesystemStorage;
use Brainbits\Blocking\Exception\DirectoryNotWritableException;
use Brainbits\Blocking\Exception\FileNotWritableException;
use Brainbits\Blocking\Owner\OwnerInterface;
use DateTimeImmutable;
use org\bovigo\vfs\vfsStream;
use Brainbits\Blocking\Block;
use Brainbits\Blocking\Identity\Identity;
use PHPUnit\Framework\TestCase;

/**
 * Filesystem storage test
 */
class FilesystemStorageTest extends TestCase
{
    /**
     * @var FilesystemStorage
     */
    private $storage;

    /**
     * @var string
     */
    private $root;

    /**
     * @var OwnerInterface
     */
    private $owner;

    protected function setUp(): void
    {
        vfsStream::setup('blockDir');

        $this->root = vfsStream::url('blockDir');
        $this->storage = new FilesystemStorage($this->root);

        $this->owner = new Owner('dummyOwner');
    }

    public function testWriteSucceedesOnNewFile(): void
    {
        $identifier = new Identity('test_block');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $result = $this->storage->write($block);

        $this->assertTrue($result);
        $this->assertTrue(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testWriteFailsOnNonExistantDirectoryInNonWritableDirectory(): void
    {
        $this->expectException(DirectoryNotWritableException::class);

        vfsStream::setup('nonWritableDir', 0);
        $adapter = new FilesystemStorage(vfsStream::url('nonWritableDir/blockDir'));

        $identifier = new Identity('test_lock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $adapter->write($block);
    }

    public function testWriteFailsOnNonWritableDirectory(): void
    {
        $this->expectException(FileNotWritableException::class);

        vfsStream::setup('writableDir');
        mkdir(vfsStream::url('writableDir/nonWritableBlockDir'), 0);

        $adapter = new FilesystemStorage(vfsStream::url('writableDir/nonWritableBlockDir'));

        $identifier = new Identity('test_lock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $adapter->write($block);
    }

    public function testTouchSucceedesOnExistingFile(): void
    {
        $identifier = new Identity('test_lock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->touch($block);

        $this->assertTrue($result);
        $this->assertTrue(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testRemoveReturnsFalseOnNonexistingFile(): void
    {
        $identifier = new Identity('test_unlock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $result = $this->storage->remove($block);

        $this->assertFalse($result);
        $this->assertFalse(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testUnblockReturnsTrueOnExistingFile(): void
    {
        $identifier = new Identity('test_unlock');
        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $this->storage->write($block);
        $result = $this->storage->remove($block);

        $this->assertTrue($result);
        $this->assertFalse(file_exists(vfsStream::url('blockDir/' . $identifier)));
    }

    public function testExistsReturnsFalseOnNonexistingFile(): void
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
