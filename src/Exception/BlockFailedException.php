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

use Brainbits\Blocking\Identity\BlockIdentity;

use function sprintf;

class BlockFailedException extends RuntimeException
{
    public static function createAlreadyBlocked(BlockIdentity $identity): self
    {
        return new self(sprintf('Identifier %s is already blocked.', $identity));
    }
}
