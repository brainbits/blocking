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

namespace Brainbits\Blocking;

use Brainbits\Blocking\Exception\BlockFailedException;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\OwnerFactoryInterface;
use Brainbits\Blocking\Storage\StorageInterface;

final readonly class Blocker
{
    public function __construct(
        private StorageInterface $storage,
        private OwnerFactoryInterface $ownerFactory,
        private int $defaultTtl = 60,
    ) {
    }

    public function block(BlockIdentity $identifier, int|null $ttl = null): Block
    {
        $block = $this->tryBlock($identifier, $ttl);

        if ($block === null) {
            throw BlockFailedException::createAlreadyBlocked($identifier);
        }

        return $block;
    }

    public function tryBlock(BlockIdentity $identifier, int|null $ttl = null): Block|null
    {
        $owner = $this->ownerFactory->createOwner();

        if ($this->isBlocked($identifier)) {
            $block = $this->getBlock($identifier);

            if (!$block || !$block->isOwnedBy($owner)) {
                return null;
            }

            $this->storage->touch($block, $ttl ?? $this->defaultTtl);

            return $block;
        }

        $block = new Block($identifier, $owner);

        $this->storage->write($block, $ttl ?? $this->defaultTtl);

        return $block;
    }

    public function unblock(BlockIdentity $identifier): Block|null
    {
        $block = $this->getBlock($identifier);
        if ($block === null) {
            return null;
        }

        $this->storage->remove($block);

        return $block;
    }

    public function isBlocked(BlockIdentity $identifier): bool
    {
        $block = $this->storage->get($identifier);

        return $block !== null;
    }

    public function getBlock(BlockIdentity $identifier): Block|null
    {
        if (!$this->isBlocked($identifier)) {
            return null;
        }

        return $this->storage->get($identifier);
    }
}
