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

namespace Brainbits\Blocking\Exception;

/**
 * Input/output exception.
 */
class IOException extends RuntimeException
{
    public static function createWriteFailed(string $filename): self
    {
        return new self("Write file $filename failed.");
    }

    public static function createTouchFailed(string $filename): self
    {
        return new self("Touch file $filename failed.");
    }

    public static function createUnlinFailed(string $filename): self
    {
        return new self("Unlink file $filename failed.");
    }
}
