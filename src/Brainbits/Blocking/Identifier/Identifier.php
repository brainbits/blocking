<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
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