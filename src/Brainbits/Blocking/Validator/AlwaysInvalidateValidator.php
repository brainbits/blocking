<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
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