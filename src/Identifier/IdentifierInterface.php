<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
