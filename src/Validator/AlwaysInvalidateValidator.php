<?php

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
 * Always invalidate validator
 * This validator always invalidates an existing block
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AlwaysInvalidateValidator implements ValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(BlockInterface $block)
    {
        return false;
    }
}
