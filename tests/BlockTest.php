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
use Brainbits\Blocking\Identity\IdentityInterface;
use Brainbits\Blocking\Owner\OwnerInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Block test
 */
class BlockTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var IdentityInterface|ObjectProphecy
     */
    private $identifier;

    /**
     * @var OwnerInterface|ObjectProphecy
     */
    private $owner;

    protected function setUp(): void
    {
        $this->identifier = $this->prophesize(IdentityInterface::class)->reveal();

        $this->owner = $this->prophesize(OwnerInterface::class);
        $this->owner->__toString()
            ->willReturn('dummyOwner');
    }

    public function testConstruct(): void
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->assertInstanceOf(Block::class, $block);
    }

    public function testGetIdentifierReturnsCorrectValue(): void
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->assertSame($this->identifier, $block->getIdentity());
    }

    public function testGetOwnerReturnsCorrectValue(): void
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->assertSame($this->owner->reveal(), $block->getOwner());
    }

    public function testIsOwnedByReturnsTrue(): void
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $this->owner->equals($this->owner->reveal())
            ->willReturn(true);

        $this->assertTrue($block->isOwnedBy($this->owner->reveal()));
    }

    public function testIsOwnedByReturnsFalse(): void
    {
        $block = new Block($this->identifier, $this->owner->reveal(), new DateTimeImmutable());

        $owner = $this->prophesize(OwnerInterface::class);
        $owner->__toString()
            ->willReturn('dummyOwner');

        $this->owner->equals($owner->reveal())
           ->willReturn(false);

        $this->assertFalse($block->isOwnedBy($owner->reveal()));
    }

    public function testGetCreatedAtReturnsCorrectValue(): void
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner->reveal(), $createdAt);
        $result = $block->getCreatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame($createdAt, $result);
    }

    public function testGetUpdatedAtReturnsCreatedAtValueAfterInstanciation(): void
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner->reveal(), $createdAt);
        $result = $block->getUpdatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame($createdAt, $result);
    }

    public function testTouchUpdatesValue(): void
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
