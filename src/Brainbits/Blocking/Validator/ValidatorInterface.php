<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Validator;

use Brainbits\Blocking\BlockInterface;

/**
 * Block validator interface
 * Tests if a block is valid
 *
 * @author  Stephan Wentz <sw@brainbits.net>
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