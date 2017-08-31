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

use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use DateTimeImmutable;

/**
 * Standard block.
 */
class Block implements BlockInterface
{
    private $identifier;
    private $owner;
    private $createdAt;
    private $updatedAt;

    public function __construct(IdentifierInterface $identifier, OwnerInterface $owner, DateTimeImmutable $createdAt)
    {
        $this->identifier = $identifier;
        $this->owner = $owner;
        $this->createdAt = $createdAt;
        $this->updatedAt = $createdAt;
    }

    public function getIdentifier(): IdentifierInterface
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

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        trigger_error('Use touch() insted of setUpdatedAt()', E_USER_DEPRECATED);

        $this->touch($updatedAt);
    }

    public function touch(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
