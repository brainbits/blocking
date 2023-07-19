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

namespace Brainbits\Blocking\Validator;

use Brainbits\Blocking\Block;

/**
 * Null validator.
 * This validator always validates an existing block.
 */
final readonly class AlwaysValidateValidator implements ValidatorInterface
{
    public function validate(Block $block): bool
    {
        return true;
    }
}
