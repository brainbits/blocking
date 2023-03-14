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

/**
 * Value owner factory.
 */
class ValueOwnerFactory implements OwnerFactoryInterface
{
    public function __construct(private string $value)
    {
    }

    public function createOwner(): OwnerInterface
    {
        return new Owner($this->value);
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
