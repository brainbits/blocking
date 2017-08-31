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
 * Block interface.
 */
interface BlockInterface
{
    public function getIdentifier(): IdentifierInterface;

    public function getOwner(): OwnerInterface;

    public function isOwnedBy(OwnerInterface $owner): bool;

    public function getCreatedAt(): DateTimeImmutable;

    public function getUpdatedAt(): DateTimeImmutable;

    /**
     * @deprecated Use touch()
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void;

    public function touch(DateTimeImmutable $updatedAt): void;
}
