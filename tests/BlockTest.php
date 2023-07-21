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

namespace Brainbits\Blocking\Tests;

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    private BlockIdentity $identifier;

    private Owner $owner;

    protected function setUp(): void
    {
        $this->identifier = new BlockIdentity('foo');
        $this->owner = new Owner('dummyOwner');
    }

    public function testConstruct(): void
    {
        $block = new Block($this->identifier, $this->owner, new DateTimeImmutable());

        $this->assertInstanceOf(Block::class, $block);
    }

    public function testGetIdentifierReturnsCorrectValue(): void
    {
        $block = new Block($this->identifier, $this->owner, new DateTimeImmutable());

        $this->assertSame($this->identifier, $block->getIdentity());
    }

    public function testGetOwnerReturnsCorrectValue(): void
    {
        $block = new Block($this->identifier, $this->owner, new DateTimeImmutable());

        $this->assertSame($this->owner, $block->getOwner());
    }

    public function testIsOwnedByReturnsTrue(): void
    {
        $block = new Block($this->identifier, $this->owner, new DateTimeImmutable());

        $this->assertTrue($block->isOwnedBy($this->owner));
    }

    public function testIsOwnedByReturnsFalse(): void
    {
        $block = new Block($this->identifier, $this->owner);

        $this->assertFalse($block->isOwnedBy(new Owner('otherOwner')));
    }
}
