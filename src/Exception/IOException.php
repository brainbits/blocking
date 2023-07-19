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

namespace Brainbits\Blocking\Exception;

use function sprintf;

class IOException extends RuntimeException
{
    public static function getFailed(string $identifier): self
    {
        return new self(sprintf('Get %s failed.', $identifier));
    }

    public static function writeFailed(string $identifier): self
    {
        return new self(sprintf('Write %s failed.', $identifier));
    }

    public static function touchFailed(string $identifier): self
    {
        return new self(sprintf('Touch %s failed.', $identifier));
    }

    public static function removeFailed(string $identifier): self
    {
        return new self(sprintf('Unlink %s failed.', $identifier));
    }
}
