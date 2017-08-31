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

namespace Brainbits\Blocking\Validator;

use Brainbits\Blocking\BlockInterface;

/**
 * Always invalidate validator.
 * This validator always invalidates an existing block.
 */
class AlwaysInvalidateValidator implements ValidatorInterface
{
    public function validate(BlockInterface $block): bool
    {
        return false;
    }
}
