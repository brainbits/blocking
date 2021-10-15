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

/**
 * Block identity interface.
 */
interface IdentityInterface
{
    public function equals(IdentityInterface $identifier): bool;

    public function __toString(): string;
}
