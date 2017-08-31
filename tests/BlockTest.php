<?php

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
use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Block test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BlockTest extends TestCase
{
    /**
     * @var IdentifierInterface|ObjectProphecy
     */
    private $identifier;

    /**
     * @var OwnerInterface|ObjectProphecy
     */
    private $owner;

    public function setUp()
    {
        $this->identifier = $this->prophesize(IdentifierInterface::class)->reveal();

        $this->owner = $this->prophesize(OwnerInterface::class);
        $this->owner->__toString()
            ->willReturn('dummyOwner');
    }

    public function testConstruct()
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->assertInstanceOf(Block::class, $block);
    }

    public function testGetIdentifierReturnsCorrectValue()
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->assertSame($this->identifier, $block->getIdentifier());
    }

    public function testGetOwnerReturnsCorrectValue()
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->assertSame($this->owner->reveal(), $block->getOwner());
    }

    public function testIsOwnedByReturnsTrue()
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->owner->equals($this->owner->reveal())
            ->willReturn(true);

        $this->assertTrue($block->isOwnedBy($this->owner->reveal()));
    }

    public function testIsOwnedByReturnsFalse()
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $owner = $this->prophesize(OwnerInterface::class);
        $owner->__toString()
            ->willReturn('dummyOwner');

        $this->owner->equals($owner->reveal())
           ->willReturn(false);

        $this->assertFalse($block->isOwnedBy($owner->reveal()));
    }

    public function testGetCreatedAtReturnsCorrectValue()
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner->reveal(), $createdAt);
        $result = $block->getCreatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame($createdAt, $result);
    }

    public function testGetUpdatedAtReturnsCreatedAtValueAfterInstanciation()
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner->reveal(), $createdAt);
        $result = $block->getUpdatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame($createdAt, $result);
    }

    public function testTouchUpdatesValue()
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner->reveal(), $createdAt);

        $updatedAt = new DateTimeImmutable();

        $block->touch($updatedAt);
        $result = $block->getUpdatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertNotSame($createdAt, $result);
        $this->assertSame($updatedAt, $result);
    }
}
