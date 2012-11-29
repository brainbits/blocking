<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking;

use Brainbits\Blocking\Identifier\IdentifierInterface;

/**
 * Blockable interface
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
interface BlockableInterface
{
    /**
     * Return block identifier
     *
     * @return IdentifierInterface
     */
    public function getBlockIdentifier();
}