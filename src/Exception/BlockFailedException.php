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

use Brainbits\Blocking\Identifier\IdentifierInterface;

/**
 * Block failed exception.
 */
class BlockFailedException extends RuntimeException
{
    public static function createAlreadyBlocked(IdentifierInterface $identifier): self
    {
        return new self("Identifier $identifier is already blocked.");
    }
}
