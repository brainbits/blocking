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
use Brainbits\Blocking\Identifier\IdentifierInterface;
use DateTimeImmutable;

/**
 * In memory block storage.
 * Uses an internal array for storing block information.
 */
class InMemoryStorage implements StorageInterface
{
    /**
     * @var BlockInterface[]
     */
    private $blocks;

    public function __construct(BlockInterface ...$blocks)
    {
        foreach ($blocks as $block) {
            $this->blocks[(string) $block->getIdentifier()] = $block;
        }
    }

    public function write(BlockInterface $block): bool
    {
        $this->blocks[(string) $block->getIdentifier()] = $block;

        return true;
    }

    public function touch(BlockInterface $block): bool
    {
        $this->blocks[(string) $block->getIdentifier()]->touch(new DateTimeImmutable());

        return true;
    }

    public function remove(BlockInterface $block): bool
    {
        if (!$this->exists($block->getIdentifier())) {
            return false;
        }

        unset($this->blocks[(string) $block->getIdentifier()]);

        return true;
    }

    public function exists(IdentifierInterface $identifier): bool
    {
        return isset($this->blocks[(string) $identifier]);
    }

    public function get(IdentifierInterface $identifier): ?BlockInterface
    {
        if (!$this->exists($identifier)) {
            return null;
        }

        $block = $this->blocks[(string) $identifier];

        $block->touch(new DateTimeImmutable());

        return $block;
    }
}
