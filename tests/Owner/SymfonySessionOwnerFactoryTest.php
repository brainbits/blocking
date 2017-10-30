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

use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Owner\SymfonySessionOwnerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Symfony session owner test
 */
class SymfonySessionOwnerFactoryTest extends TestCase
{
    public function testToString()
    {
        $factory = new SymfonySessionOwnerFactory($this->createSession('foo'));
        $owner = $factory->createOwner();

        $this->assertEquals($owner, new Owner('foo'));
    }

    private function createSession($sessionId)
    {
        $session = $this->prophesize(SessionInterface::class);
        $session->getId()
            ->willReturn($sessionId);

        return $session->reveal();
    }
}
