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
use Brainbits\Blocking\Identity\IdentityInterface;
use Brainbits\Blocking\Owner\OwnerFactoryInterface;
use Brainbits\Blocking\Storage\StorageInterface;
use Brainbits\Blocking\Validator\ValidatorInterface;
use DateTimeImmutable;

/**
 * Blocker.
 */
class Blocker
{
    private StorageInterface $storage;
    private OwnerFactoryInterface $ownerFactory;
    private ValidatorInterface $validator;

    public function __construct(
        StorageInterface $adapter,
        OwnerFactoryInterface $ownerFactory,
        ValidatorInterface $validator
    ) {
        $this->storage = $adapter;
        $this->ownerFactory = $ownerFactory;
        $this->validator = $validator;
    }

    public function block(IdentityInterface $identifier): BlockInterface
    {
        $block = $this->tryBlock($identifier);

        if ($block === null) {
            throw BlockFailedException::createAlreadyBlocked($identifier);
        }

        return $block;
    }

    public function tryBlock(IdentityInterface $identifier): ?BlockInterface
    {
        $owner = $this->ownerFactory->createOwner();

        if ($this->isBlocked($identifier)) {
            $block = $this->getBlock($identifier);
            if (!$block->isOwnedBy($owner)) {
                return null;
            }

            $this->storage->touch($block);

            return $block;
        }

        $block = new Block($identifier, $owner, new DateTimeImmutable());

        $this->storage->write($block);

        return $block;
    }

    public function unblock(IdentityInterface $identifier): ?BlockInterface
    {
        $block = $this->getBlock($identifier);
        if ($block === null) {
            return null;
        }

        $this->storage->remove($block);

        return $block;
    }

    public function isBlocked(IdentityInterface $identifier): bool
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

    public function getBlock(IdentityInterface $identifier): ?BlockInterface
    {
        if (!$this->isBlocked($identifier)) {
            return null;
        }

        return $this->storage->get($identifier);
    }
}
