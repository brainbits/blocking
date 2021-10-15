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

namespace Brainbits\Blocking\Owner;

class Owner implements OwnerInterface
{
    private string $ownerValue;

    public function __construct(string $ownerValue)
    {
        $this->ownerValue = $ownerValue;
    }

    public function equals(OwnerInterface $owner): bool
    {
        return (string) $this === (string) $owner;
    }

    public function __toString(): string
    {
        return $this->ownerValue;
    }
}
