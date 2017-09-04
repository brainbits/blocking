<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests\Owner;

use Brainbits\Blocking\Owner\SymfonySessionOwner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Symfony session owner test
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class SymfonySessionOwnerTest extends TestCase
{
    public function testToString()
    {
        $owner = new SymfonySessionOwner($this->createSession('foo'));

        $this->assertEquals('foo', (string) $owner);
    }

    public function testEquals()
    {
        $identifier1 = new SymfonySessionOwner($this->createSession('foo'));
        $identifier2 = new SymfonySessionOwner($this->createSession('foo'));
        $identifier3 = new SymfonySessionOwner($this->createSession('bar'));

        $this->assertTrue($identifier1->equals($identifier2));
        $this->assertFalse($identifier1->equals($identifier3));
        $this->assertFalse($identifier2->equals($identifier3));
    }

    private function createSession($sessionId)
    {
        $session = $this->prophesize(SessionInterface::class);
        $session->getId()
            ->willReturn($sessionId);

        return $session->reveal();
    }
}
