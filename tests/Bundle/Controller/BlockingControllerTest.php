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

namespace Brainbits\Blocking\Bundle\Tests\Controller;

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\Bundle\Controller\BlockingController;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Owner\ValueOwnerFactory;
use Brainbits\Blocking\Storage\InMemoryStorage;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpFoundation\JsonResponse;

use function json_decode;

final class BlockingControllerTest extends TestCase
{
    private BlockingController $controller;
    private ClockInterface $clock;

    protected function setUp(): void
    {
        $this->clock = new MockClock('now', new DateTimeZone('UTC'));

        $block = new Block(
            new BlockIdentity('foo'),
            new Owner('baz'),
        );

        $storage = new InMemoryStorage($this->clock);
        $storage->addBlock($block, 10, $this->clock->now());
        $blocker = new Blocker(
            $storage,
            new ValueOwnerFactory('bar'),
        );

        $this->controller = new BlockingController($blocker);
    }

    public function testBlockSuccessAction(): void
    {
        $response = $this->controller->blockAction('new');

        $this->assertInstanceOf(JsonResponse::class, $response);

        $result = (array) json_decode((string) $response->getContent(), true);

        $this->assertTrue($result['success'] ?? null);
    }

    public function testBlockFailureAction(): void
    {
        $response = $this->controller->blockAction('foo');

        $this->assertInstanceOf(JsonResponse::class, $response);

        $result = (array) json_decode((string) $response->getContent(), true);

        $this->assertFalse($result['success'] ?? null);
    }

    public function testUnblockSuccessAction(): void
    {
        $response = $this->controller->unblockAction('foo');

        $this->assertInstanceOf(JsonResponse::class, $response);

        $result = (array) json_decode((string) $response->getContent(), true);

        $this->assertTrue($result['success'] ?? null);
    }

    public function testUnblockFailureAction(): void
    {
        $response = $this->controller->unblockAction('new');

        $this->assertInstanceOf(JsonResponse::class, $response);

        $result = (array) json_decode((string) $response->getContent(), true);

        $this->assertFalse($result['success'] ?? null);
    }
}
