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

namespace Brainbits\Blocking;

use Brainbits\Blocking\Storage\StorageInterface;
use Brainbits\Blocking\Exception\BlockFailedException;
use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use Brainbits\Blocking\Validator\ValidatorInterface;
use DateTimeImmutable;

/**
 * Blocker.
 */
class Blocker
{
    private $storage;
    private $owner;
    private $validator;

    public function __construct(StorageInterface $adapter, OwnerInterface $owner, ValidatorInterface $validator)
    {
        $this->storage = $adapter;
        $this->owner = $owner;
        $this->validator = $validator;
    }

    public function block(IdentifierInterface $identifier): BlockInterface
    {
        $block = $this->tryBlock($identifier);

        if ($block === null) {
            throw BlockFailedException::createAlreadyBlocked($identifier);
        }

        return $block;
    }

    public function tryBlock(IdentifierInterface $identifier): ?BlockInterface
    {
        if ($this->isBlocked($identifier)) {
            $block = $this->getBlock($identifier);
            if (!$block->isOwnedBy($this->owner)) {
                return null;
            }
            $this->storage->touch($block);

            return $block;
        }

        $block = new Block($identifier, $this->owner, new DateTimeImmutable());

        $this->storage->write($block);

        return $block;
    }


    public function unblock(IdentifierInterface $identifier): ?BlockInterface
    {
        $block = $this->getBlock($identifier);
        if (null === $block) {
            return null;
        }

        $this->storage->remove($block);

        return $block;
    }

    public function isBlocked(IdentifierInterface $identifier): bool
    {
        $exists = $this->storage->exists($identifier);

        if (!$exists) {
            return false;
        }

        $block = $this->storage->get($identifier);
        $valid = $this->validator->validate($block);

        if ($valid) {
            return true;
        }

        $this->storage->remove($block);

        return false;
    }

    public function getBlock(IdentifierInterface $identifier): ?BlockInterface
    {
        if (!$this->isBlocked($identifier)) {
            return null;
        }

        return $this->storage->get($identifier);
    }
}
