<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Owner;

/**
 * Block owner interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface OwnerInterface
{
    /**
     * Return string representation
     * @return string
     */
    public function __toString();
}