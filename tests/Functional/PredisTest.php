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

use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\Exception\BlockFailedException;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\ValueOwnerFactory;
use Brainbits\Blocking\Storage\PredisStorage;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Predis\ClientInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

use function getenv;
use function json_encode;

/** @group redis */
class PredisTest extends TestCase
{
    private ClockInterface $clock;
    private ClientInterface $client;

    public function setUp(): void
    {
        $this->clock = new MockClock();

        if (!getenv('REDIS_DSN')) {
            $this->markTestSkipped('REDIS_DSN is needed for PredisTest');
        }

        $this->client = new Client(getenv('REDIS_DSN'));
        $this->client->flushall();
    }

    public function testSelfBlock(): void
    {
        $blocker = new Blocker(
            new PredisStorage($this->client),
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
        $this->expectException(BlockFailedException::class);
        $this->expectExceptionMessage('Identifier my_item is already blocked.');

        $this->client->set('block:my_item', json_encode(['identity' => 'my_item', 'owner' => 'other_owner']));

        $blocker = new Blocker(
            new PredisStorage($this->client),
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));
    }

    public function testOtherBlockWithRemove(): void
    {
        $this->client->set('block:my_item', json_encode(['identity' => 'my_item', 'owner' => 'other_owner']));

        $blocker = new Blocker(
            new PredisStorage($this->client),
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
            new PredisStorage($this->client),
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
        $this->client->set(
            'block:my_item',
            json_encode(['identity' => 'my_item', 'owner' => 'other_owner']),
            'EXAT',
            $this->clock->now()->modify('-1 minute')->format('U'),
        );

        $blocker = new Blocker(
            new PredisStorage($this->client),
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));
    }
}
