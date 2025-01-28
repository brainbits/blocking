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

namespace Brainbits\Blocking\Tests\Functional;

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\Exception\AlreadyBlockedException;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Owner\ValueOwnerFactory;
use Brainbits\Blocking\Storage\InMemoryStorage;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

class InMemoryTest extends TestCase
{
    private ClockInterface $clock;

    public function setUp(): void
    {
        $this->clock = new MockClock('now', new DateTimeZone('UTC'));
    }

    public function testSelfBlock(): void
    {
        $blocker = new Blocker(
            new InMemoryStorage($this->clock),
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));
        $blocker->block(new BlockIdentity('my_item'));
        $blocker->block(new BlockIdentity('my_item'));

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->unblock(new BlockIdentity('my_item'));

        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item')));
    }

    public function testOtherBlock(): void
    {
        $this->expectException(AlreadyBlockedException::class);
        $this->expectExceptionMessage('Identifier my_item is already blocked.');

        $storage = new InMemoryStorage($this->clock);
        $storage->addBlock(
            new Block(new BlockIdentity('my_item'), new Owner('other_owner')),
            10,
            $this->clock->now(),
        );

        $blocker = new Blocker(
            $storage,
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));
    }

    public function testOtherBlockWithRemove(): void
    {
        $storage = new InMemoryStorage($this->clock);
        $storage->addBlock(
            new Block(new BlockIdentity('my_item'), new Owner('other_owner')),
            10,
            $this->clock->now(),
        );

        $blocker = new Blocker(
            $storage,
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->unblock(new BlockIdentity('my_item'));

        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));
    }

    public function testMultipleBlocks(): void
    {
        $blocker = new Blocker(
            new InMemoryStorage($this->clock),
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item_1')));
        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item_2')));
        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item_3')));

        $blocker->block(new BlockIdentity('my_item_1'));
        $blocker->block(new BlockIdentity('my_item_2'));
        $blocker->block(new BlockIdentity('my_item_3'));

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item_1')));
        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item_2')));
        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item_3')));
    }

    public function testExpiredBlock(): void
    {
        $storage = new InMemoryStorage($this->clock);
        $storage->addBlock(
            new Block(new BlockIdentity('my_item'), new Owner('other_owner')),
            10,
            $this->clock->now()->modify('-1 minute'),
        );

        $blocker = new Blocker(
            $storage,
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));
    }
}
