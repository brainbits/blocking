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

namespace Brainbits\Blocking\Identity;

final class BlockIdentity
{
    public function __construct(private string $identityValue)
    {
    }

    public function equals(self $identifier): bool
    {
        return (string) $identifier === (string) $this;
    }

    public function __toString(): string
    {
        return $this->identityValue;
    }
}
