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

namespace Brainbits\Blocking\Storage;

use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Exception\DirectoryNotWritableException;
use Brainbits\Blocking\Exception\FileNotWritableException;
use Brainbits\Blocking\Exception\IOException;
use Brainbits\Blocking\Exception\UnserializeFailedException;
use Brainbits\Blocking\Identity\IdentityInterface;
use DateTimeImmutable;

use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function is_writable;
use function mkdir;
use function rtrim;
use function serialize;
use function touch;
use function unlink;
use function unserialize;

/**
 * Filesystem block storage.
 * Uses files for storing block information.
 */
class FilesystemStorage implements StorageInterface
{
    private string $root;

    public function __construct(string $root)
    {
        $this->root = rtrim($root, '/');
    }

    public function write(BlockInterface $block): bool
    {
        $filename = $this->getFilename($block->getIdentity());

        if (file_put_contents($filename, serialize($block)) === false) {
            throw IOException::createWriteFailed($filename);
        }

        return true;
    }

    public function touch(BlockInterface $block): bool
    {
        $filename = $this->getFilename($block->getIdentity());

        if (touch($filename) === false) {
            throw IOException::createTouchFailed($filename);
        }

        $updatedAt = DateTimeImmutable::createFromFormat('U', (string) filemtime($filename));

        $block->touch($updatedAt);

        return true;
    }

    public function remove(BlockInterface $block): bool
    {
        if (!$this->exists($block->getIdentity())) {
            return false;
        }

        $filename = $this->getFilename($block->getIdentity());
        if (unlink($filename) === false) {
            if (file_exists($filename)) {
                throw IOException::createUnlinkFailed($filename);
            }
        }

        return true;
    }

    public function exists(IdentityInterface $identifier): bool
    {
        $filename = $this->getFilename($identifier);

        return file_exists($filename);
    }

    public function get(IdentityInterface $identifier): ?BlockInterface
    {
        if (!$this->exists($identifier)) {
            return null;
        }

        $filename = $this->getFilename($identifier);
        $content = file_get_contents($filename);
        $updatedAt = DateTimeImmutable::createFromFormat('U', (string) filemtime($filename));
        $block = unserialize($content);
        if (!$block) {
            throw UnserializeFailedException::createFromInput($content);
        }

        $block->touch($updatedAt);

        return $block;
    }

    private function getFilename(IdentityInterface $identifier): string
    {
        return $this->ensureFileIsWritable($this->ensureDirectoryExists($this->root) . '/' . $identifier);
    }

    private function ensureFileIsWritable(string $file): string
    {
        if (!is_writable(dirname($file))) {
            throw FileNotWritableException::create($file);
        }

        return $file;
    }

    private function ensureDirectoryExists(string $dirname): string
    {
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            throw DirectoryNotWritableException::create($dirname);
        }

        return $dirname;
    }
}
