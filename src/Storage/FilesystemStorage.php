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

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Exception\DirectoryNotWritableException;
use Brainbits\Blocking\Exception\FileNotWritableException;
use Brainbits\Blocking\Exception\IOException;
use Brainbits\Blocking\Exception\UnserializeFailedException;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

use function assert;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function is_string;
use function is_writable;
use function json_decode;
use function json_encode;
use function mkdir;
use function rtrim;
use function unlink;

/**
 * Filesystem block storage.
 * Uses files for storing block information.
 */
final class FilesystemStorage implements StorageInterface
{
    public function __construct(
        private ClockInterface $clock,
        private string $root,
    ) {
        $this->root = rtrim($root, '/');
    }

    public function write(Block $block, int $ttl): bool
    {
        $identity = $block->getIdentity();

        $filename = $this->getFilename($identity);
        $metaFilename = $filename . '.meta';

        $content = json_encode([
            'identity' => (string) $identity,
            'owner' => (string) $block->getOwner(),
        ]);

        $metaContent = json_encode([
            'ttl' => $ttl,
            'updatedAt' => $this->clock->now()->format('c'),
        ]);

        if (file_put_contents($filename, $content) === false) {
            throw IOException::writeFailed($filename);
        }

        if (file_put_contents($metaFilename, $metaContent) === false) {
            throw IOException::writeFailed($metaFilename);
        }

        return true;
    }

    public function touch(Block $block, int $ttl): bool
    {
        $identity = $block->getIdentity();

        if (!$this->exists($identity)) {
            return false;
        }

        $filename = $this->getFilename($block->getIdentity());
        $metaFilename = $filename . '.meta';

        $metaContent = json_encode([
            'ttl' => $ttl,
            'updatedAt' => $this->clock->now()->format('c'),
        ]);

        if (file_put_contents($metaFilename, $metaContent) === false) {
            throw IOException::writeFailed($metaFilename);
        }

        return true;
    }

    public function remove(Block $block): bool
    {
        if (!$this->exists($block->getIdentity())) {
            return false;
        }

        $filename = $this->getFilename($block->getIdentity());
        $metaFilename = $filename . '.meta';

        if (unlink($filename) === false) {
            if (file_exists($filename)) {
                throw IOException::removeFailed($filename);
            }
        }

        if (unlink($metaFilename) === false) {
            if (file_exists($metaFilename)) {
                throw IOException::removeFailed($metaFilename);
            }
        }

        return true;
    }

    public function exists(BlockIdentity $identity): bool
    {
        $filename = $this->getFilename($identity);
        $metaFilename = $filename . '.meta';

        if (!file_exists($filename) || !file_exists($metaFilename)) {
            return false;
        }

        $metaContent = file_get_contents($metaFilename);
        assert(is_string($metaContent));
        assert($metaContent !== '');
        $metaData = json_decode($metaContent, true);
        assert(is_array($metaData));

        $now = $this->clock->now();

        $expiresAt = (new DateTimeImmutable((string) $metaData['updatedAt'], $now->getTimezone()))
            ->modify('+' . $metaData['ttl'] . ' seconds');

        return $expiresAt > $now;
    }

    public function get(BlockIdentity $identity): Block|null
    {
        if (!$this->exists($identity)) {
            return null;
        }

        $filename = $this->getFilename($identity);

        $content = file_get_contents($filename);

        if (!$content) {
            throw UnserializeFailedException::createFromInput($content);
        }

        $data = json_decode($content, true);

        assert(is_array($data));
        assert($data['identity'] ?? false);
        assert($data['owner'] ?? false);

        return new Block(
            new BlockIdentity($data['identity']),
            new Owner($data['owner']),
        );
    }

    private function getFilename(BlockIdentity $identifier): string
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
