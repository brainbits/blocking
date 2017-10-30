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

use Brainbits\Blocking\Exception\NoTokenFoundException;
use Brainbits\Blocking\Exception\NoUserFoundException;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Owner\SymfonyTokenOwnerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Symfony token owner factory test
 */
class SymfonyTokenOwnerFactoryTest extends TestCase
{
    public function testCreateOwner()
    {
        $factory = new SymfonyTokenOwnerFactory($this->createTokenStorage('foo'));
        $owner = $factory->createOwner();

        $this->assertEquals($owner, new Owner('foo'));
    }

    public function testE()
    {
        $this->expectException(NoTokenFoundException::class);

        $factory = new SymfonyTokenOwnerFactory($this->createTokenStorage('foo', false, false));
        $factory->createOwner();
    }

    public function testE2()
    {
        $this->expectException(NoUserFoundException::class);

        $factory = new SymfonyTokenOwnerFactory($this->createTokenStorage('bar', true, false));

        $factory->createOwner();
    }

    private function createTokenStorage(string $username, bool $createToken = true, bool $createUser = true)
    {
        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()
            ->willReturn($username);

        $token = $this->prophesize(TokenInterface::class);
        if ($createUser && $createToken) {
            $token->getUser()
                ->willReturn($user->reveal());
        }

        $storage = $this->prophesize(TokenStorageInterface::class);
        if ($createToken) {
            $storage->getToken()
                ->willReturn($token->reveal());
        }

        return $storage->reveal();
    }
}
