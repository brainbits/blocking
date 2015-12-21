<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests\Identifier;

use Brainbits\Blocking\Identifier\Identifier;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Identifier test
 *
 * @author Stephan Wentz <sw@brainbits.net>
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
