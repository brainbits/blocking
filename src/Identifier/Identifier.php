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

namespace Brainbits\Blocking\Identifier;

/**
 * Standard identifier.
 */
class Identifier implements IdentifierInterface
{
    private $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function equals(IdentifierInterface $identifier): bool
    {
        return (string) $identifier === (string) $this->identifier;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
