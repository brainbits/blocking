<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * (c) 2012-2013 brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Identifier;

/**
 * Default identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Identifier implements IdentifierInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param string $type
     * @param string $id
     */
    public function __construct($type, $id)
    {
        $this->identifier = $type . '__' . $id;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->identifier;
    }
}
