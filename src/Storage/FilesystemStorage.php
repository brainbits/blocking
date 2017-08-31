<?php

declare(strict_types = 1);

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
use Brainbits\Blocking\Identifier\IdentifierInterface;
use DateTimeImmutable;

/**
 * Filesystem block storage.
 * Uses files for storing block information.
 */
class FilesystemStorage implements StorageInterface
{
    private $root;

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    public function write(BlockInterface $block): bool
    {
        $filename = $this->getFilename($block->getIdentifier());

        if (false === file_put_contents($filename, serialize($block))) {
            throw IOException::createWriteFailed($filename);
        }

        return true;
    }

    public function touch(BlockInterface $block): bool
    {
        $filename = $this->getFilename($block->getIdentifier());

        if (false === touch($filename)) {
            throw IOException::createTouchFailed($filename);
        }

        $updatedAt = DateTimeImmutable::createFromFormat('U', (string) filemtime($filename));

        $block->touch($updatedAt);

        return true;
    }

    public function remove(BlockInterface $block): bool
    {
        if (!$this->exists($block->getIdentifier())) {
            return false;
        }

        $filename = $this->getFilename($block->getIdentifier());
        if (false === unlink($filename)) {
            throw IOException::createUnlinFailed($filename);
        }

        return true;
    }

    public function exists(IdentifierInterface $identifier): bool
    {
        $filename = $this->getFilename($identifier);

        return file_exists($filename);
    }

    public function get(IdentifierInterface $identifier): ?BlockInterface
    {
        if (!$this->exists($identifier)) {
            return null;
        }

        $filename = $this->getFilename($identifier);
        $content = file_get_contents($filename);
        $updatedAt = DateTimeImmutable::createFromFormat('U', (string) filemtime($filename));
        $block = unserialize($content);
        $block->touch($updatedAt);

        return $block;
    }

    private function getFilename(IdentifierInterface $identifier): string
    {
        return $this->ensureFileIsWritable($this->ensureDirectoryExists($this->root).'/'.$identifier);
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
