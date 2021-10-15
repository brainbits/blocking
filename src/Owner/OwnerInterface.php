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
 * Block owner interface.
 */
interface OwnerInterface
{
    public function equals(OwnerInterface $owner): bool;

    public function __toString(): string;
}
