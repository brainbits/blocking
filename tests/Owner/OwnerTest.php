<?php

declare(strict_types=1);

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests\Owner;

use Brainbits\Blocking\Owner\Owner;
use PHPUnit\Framework\TestCase;

final class OwnerTest extends TestCase
{
    public function testConstruct(): void
    {
        $owner = new Owner('test_123');

        $this->assertInstanceOf(Owner::class, $owner);
    }

    public function testEquals(): void
    {
        $owner1 = new Owner('foo');
        $owner2 = new Owner('foo');
        $owner3 = new Owner('bar');

        $this->assertTrue($owner1->equals($owner2));
        $this->assertFalse($owner1->equals($owner3));
        $this->assertFalse($owner2->equals($owner3));
    }

    public function testToString(): void
    {
        $owner = new Owner('test_123');

        $this->assertEquals('test_123', $owner);
    }
}
