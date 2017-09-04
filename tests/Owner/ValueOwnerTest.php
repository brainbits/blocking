<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests\Owner;

use Brainbits\Blocking\Owner\ValueOwner;
use PHPUnit\Framework\TestCase;

/**
 * Value owner test
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class ValueOwnerTest extends TestCase
{
    public function testToString()
    {
        $owner = new ValueOwner('abcdef');

        $this->assertEquals('abcdef', (string) $owner);
    }

    public function testEquals()
    {
        $identifier1 = new ValueOwner('foo');
        $identifier2 = new ValueOwner('foo');
        $identifier3 = new ValueOwner('bar');

        $this->assertTrue($identifier1->equals($identifier2));
        $this->assertFalse($identifier1->equals($identifier3));
        $this->assertFalse($identifier2->equals($identifier3));
    }
}
