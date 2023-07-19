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
use Brainbits\Blocking\Exception\IOException;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use Predis\ClientInterface;
use Predis\PredisException;

use function assert;
use function is_array;
use function json_decode;
use function json_encode;

/**
 * Predis block storage.
 * Uses predis for storing block information.
 */
final readonly class PredisStorage implements StorageInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $prefix = 'block',
    ) {
    }

    public function write(Block $block, int $ttl): bool
    {
        $identity = $block->getIdentity();

        $data = json_encode([
            'identity' => (string) $identity,
            'owner' => (string) $block->getOwner(),
        ]);

        try {
            $this->client->set($this->createKey($identity), $data, 'EX', $ttl);
        } catch (PredisException) {
            throw IOException::writeFailed((string) $identity);
        }

        return true;
    }

    public function touch(Block $block): bool
    {
        $identity = $block->getIdentity();

        if (!$this->exists($identity)) {
            return false;
        }

        try {
            $this->client->touch($this->createKey($identity));
        } catch (PredisException) {
            throw IOException::touchFailed((string) $identity);
        }

        return true;
    }

    public function remove(Block $block): bool
    {
        $identity = $block->getIdentity();

        if (!$this->exists($identity)) {
            return false;
        }

        try {
            $this->client->del($this->createKey($identity));
        } catch (PredisException) {
            throw IOException::removeFailed((string) $block->getIdentity());
        }

        return true;
    }

    public function exists(BlockIdentity $identity): bool
    {
        return (bool) $this->client->exists($this->createKey($identity));
    }

    public function get(BlockIdentity $identity): Block|null
    {
        if (!$this->exists($identity)) {
            return null;
        }

        try {
            $content = $this->client->get($this->createKey($identity));
        } catch (PredisException) {
            throw IOException::getFailed((string) $identity);
        }

        if (!$content) {
            throw IOException::getFailed((string) $identity);
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

    private function createKey(BlockIdentity $identity): string
    {
        return $this->prefix . ':' . $identity;
    }
}
