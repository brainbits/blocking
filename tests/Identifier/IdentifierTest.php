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
use PHPUnit\Framework\TestCase;

/**
 * Identifier test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IdentifierTest extends TestCase
{
    public function testConstruct()
    {
        $identifier = new Identifier('test_123');

        $this->assertInstanceOf(Identifier::class, $identifier);
    }

    public function testEquals()
    {
        $identifier1 = new Identifier('foo');
        $identifier2 = new Identifier('foo');
        $identifier3 = new Identifier('bar');

        $this->assertTrue($identifier1->equals($identifier2));
        $this->assertFalse($identifier1->equals($identifier3));
        $this->assertFalse($identifier2->equals($identifier3));
    }

    public function testToString()
    {
        $identifier = new Identifier('test_123');

        $this->assertEquals('test_123', $identifier);
    }
}
