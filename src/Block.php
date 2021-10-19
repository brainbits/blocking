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

use Brainbits\Blocking\Identity\IdentityInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use DateTimeImmutable;

/**
 * Standard block.
 */
class Block implements BlockInterface
{
    private IdentityInterface $identifier;
    private OwnerInterface $owner;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(IdentityInterface $identifier, OwnerInterface $owner, DateTimeImmutable $createdAt)
    {
        $this->identifier = $identifier;
        $this->owner = $owner;
        $this->createdAt = $createdAt;
        $this->updatedAt = $createdAt;
    }

    public function getIdentity(): IdentityInterface
    {
        return $this->identifier;
    }

    public function getOwner(): OwnerInterface
    {
        return $this->owner;
    }

    public function isOwnedBy(OwnerInterface $owner): bool
    {
        return $this->owner->equals($owner);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function touch(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
