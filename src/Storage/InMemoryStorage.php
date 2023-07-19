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
use Brainbits\Blocking\Identity\BlockIdentity;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * In memory block storage.
 * Uses an internal array for storing block information.
 */
final class InMemoryStorage implements StorageInterface
{
    /** @var array<string, array{block: Block, ttl: int, updatedAt: DateTimeImmutable}> */
    private array $blocks;

    public function __construct(
        private ClockInterface $clock,
    ) {
    }

    public function addBlock(Block $block, int $ttl, DateTimeImmutable $updatedAt): void
    {
        $this->blocks[(string) $block->getIdentity()] = [
            'block' => $block,
            'ttl' => $ttl,
            'updatedAt' => $updatedAt,
        ];
    }

    public function write(Block $block, int $ttl): bool
    {
        $this->blocks[(string) $block->getIdentity()] = [
            'block' => $block,
            'ttl' => $ttl,
            'updatedAt' => $this->clock->now(),
        ];

        return true;
    }

    public function touch(Block $block): bool
    {
        if (!$this->exists($block->getIdentity())) {
            return false;
        }

        $this->blocks[(string) $block->getIdentity()]['updatedAt'] = $this->clock->now();

        return true;
    }

    public function remove(Block $block): bool
    {
        if (!$this->exists($block->getIdentity())) {
            return false;
        }

        unset($this->blocks[(string) $block->getIdentity()]);

        return true;
    }

    public function exists(BlockIdentity $identity): bool
    {
        if (!isset($this->blocks[(string) $identity])) {
            return false;
        }

        $now = $this->clock->now();

        $metaData = $this->blocks[(string) $identity];

        $expiresAt = $metaData['updatedAt']->modify('+' . $metaData['ttl'] . ' seconds');

        return $expiresAt > $now;
    }

    public function get(BlockIdentity $identity): Block|null
    {
        if (!$this->exists($identity)) {
            return null;
        }

        return $this->blocks[(string) $identity]['block'];
    }
}
