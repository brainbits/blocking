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

namespace Brainbits\Blocking\Tests\Identifier;

use Brainbits\Blocking\Identity\BlockIdentity;
use PHPUnit\Framework\TestCase;

final class BlockIdentifierTest extends TestCase
{
    public function testConstruct(): void
    {
        $identifier = new BlockIdentity('test_123');

        $this->assertInstanceOf(BlockIdentity::class, $identifier);
    }

    public function testEquals(): void
    {
        $identifier1 = new BlockIdentity('foo');
        $identifier2 = new BlockIdentity('foo');
        $identifier3 = new BlockIdentity('bar');

        $this->assertTrue($identifier1->equals($identifier2));
        $this->assertFalse($identifier1->equals($identifier3));
        $this->assertFalse($identifier2->equals($identifier3));
    }

    public function testToString(): void
    {
        $identifier = new BlockIdentity('test_123');

        $this->assertEquals('test_123', $identifier);
    }
}
