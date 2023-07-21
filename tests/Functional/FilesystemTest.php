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
use Brainbits\Blocking\Storage\FilesystemStorage;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

use function file_put_contents;
use function json_encode;

class FilesystemTest extends TestCase
{
    private ClockInterface $clock;
    private string $root;

    public function setUp(): void
    {
        $this->clock = new MockClock();

        vfsStream::setup('blockDir');
        $this->root = vfsStream::url('blockDir');
    }

    public function testSelfBlock(): void
    {
        $blocker = new Blocker(
            new FilesystemStorage($this->clock, $this->root),
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

        file_put_contents(
            $this->root . '/my_item',
            json_encode(['identity' => 'my_item', 'owner' => 'other_owner']),
        );
        file_put_contents(
            $this->root . '/my_item.meta',
            json_encode(['ttl' => 10, 'updatedAt' => $this->clock->now()->format('c')]),
        );

        $blocker = new Blocker(
            new FilesystemStorage($this->clock, $this->root),
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));
    }

    public function testOtherBlockWithRemove(): void
    {
        file_put_contents(
            $this->root . '/my_item',
            json_encode(['identity' => 'my_item', 'owner' => 'other_owner']),
        );
        file_put_contents(
            $this->root . '/my_item.meta',
            json_encode(['ttl' => 10, 'updatedAt' => $this->clock->now()->format('c')]),
        );

        $blocker = new Blocker(
            new FilesystemStorage($this->clock, $this->root),
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
            new FilesystemStorage($this->clock, $this->root),
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
        file_put_contents(
            $this->root . '/my_item',
            json_encode(['identity' => 'my_item', 'owner' => 'other_owner']),
        );
        file_put_contents(
            $this->root . '/my_item.meta',
            json_encode(['ttl' => 10, 'updatedAt' => $this->clock->now()->modify('-1 minute')->format('c')]),
        );

        $blocker = new Blocker(
            new FilesystemStorage($this->clock, $this->root),
            new ValueOwnerFactory('my_owner'),
        );

        $this->assertFalse($blocker->isBlocked(new BlockIdentity('my_item')));

        $blocker->block(new BlockIdentity('my_item'));

        $this->assertTrue($blocker->isBlocked(new BlockIdentity('my_item')));
    }
}
