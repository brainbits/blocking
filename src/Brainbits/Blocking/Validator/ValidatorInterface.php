<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * (c) 2012-2013 brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Validator;

use Brainbits\Blocking\BlockInterface;

/**
 * Block validator interface
 * Tests if a block is valid
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ValidatorInterface
{
    /**
     * Validate block
     * Return true if an existing block is valid, false if invalid
     *
     * @param BlockInterface $block
     * @return boolean
     */
    public function validate(BlockInterface $block);
}