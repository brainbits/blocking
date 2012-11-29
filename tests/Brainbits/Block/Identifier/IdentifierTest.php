<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Identifier;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Identifier test
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class IdentifierTest extends TestCase
{
    public function testConstruct()
    {
        $identifier = new Identifier('test', 123);
    }

    public function testToString()
    {
        $identifier = new Identifier('test', 123);

        $this->assertEquals('test__123', $identifier);
    }
}