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

namespace Brainbits\Blocking\Identity;

/**
 * Standard identifier.
 */
class Identity implements IdentityInterface
{
    private $identityValue;

    public function __construct(string $identityValue)
    {
        $this->identityValue = $identityValue;
    }

    public function equals(IdentityInterface $identifier): bool
    {
        return (string) $identifier === (string) $this;
    }

    public function __toString(): string
    {
        return $this->identityValue;
    }
}
