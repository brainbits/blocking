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

use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;

final class Block
{
    public function __construct(
        private BlockIdentity $identifier,
        private Owner $owner,
    ) {
    }

    public function getIdentity(): BlockIdentity
    {
        return $this->identifier;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function isOwnedBy(Owner $owner): bool
    {
        return $this->owner->equals($owner);
    }
}
