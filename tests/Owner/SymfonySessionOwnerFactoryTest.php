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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SymfonySessionOwnerFactoryTest extends TestCase
{
    public function testToString(): void
    {
        $request = new Request();
        $request->setSession($this->createSession('foo'));

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $factory = new SymfonySessionOwnerFactory($requestStack);

        $owner = $factory->createOwner();

        $this->assertEquals($owner, new Owner('foo'));
    }

    /**
     * @return SessionInterface|MockObject
     */
    private function createSession($sessionId)
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('getId')
            ->willReturn($sessionId);

        return $session;
    }
}
