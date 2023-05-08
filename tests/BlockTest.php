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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    /**
     * @var IdentityInterface|MockObject
     */
    private $identifier;

    /**
     * @var OwnerInterface|MockObject
     */
    private $owner;

    protected function setUp(): void
    {
        $this->identifier = $this->createMock(IdentityInterface::class);

        $this->owner = $this->createMock(OwnerInterface::class);
        $this->owner->expects($this->any())
            ->method('__toString')
            ->willReturn('dummyOwner');
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

        $this->owner->expects($this->once())
            ->method('equals')
            ->with($this->owner)
            ->willReturn(true);

        $this->assertTrue($block->isOwnedBy($this->owner));
    }

    public function testIsOwnedByReturnsFalse(): void
    {
        $block = new Block($this->identifier, $this->owner, new DateTimeImmutable());

        $owner = $this->createMock(OwnerInterface::class);
        $this->owner->expects($this->any())
            ->method('__toString')
            ->willReturn('dummyOwner');

        $this->owner->expects($this->once())
            ->method('equals')
            ->with($owner)
            ->willReturn(false);

        $this->assertFalse($block->isOwnedBy($owner));
    }

    public function testGetCreatedAtReturnsCorrectValue(): void
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner, $createdAt);
        $result = $block->getCreatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame($createdAt, $result);
    }

    public function testGetUpdatedAtReturnsCreatedAtValueAfterInstanciation(): void
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner, $createdAt);
        $result = $block->getUpdatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertSame($createdAt, $result);
    }

    public function testTouchUpdatesValue(): void
    {
        $createdAt = new DateTimeImmutable();

        $block = new Block($this->identifier, $this->owner, $createdAt);

        $updatedAt = new DateTimeImmutable();

        $block->touch($updatedAt);
        $result = $block->getUpdatedAt();

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertNotSame($createdAt, $result);
        $this->assertSame($updatedAt, $result);
    }
}
