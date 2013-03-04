<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Identifier;

/**
 * Block identifier interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface IdentifierInterface
{
    /**
     * Return string representation of identifier
     *
     * @return string
     */
    public function __toString();
}