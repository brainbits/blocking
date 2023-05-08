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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Symfony token owner factory test
 */
class SymfonyTokenOwnerFactoryTest extends TestCase
{
    public function testCreateOwner(): void
    {
        $factory = new SymfonyTokenOwnerFactory($this->createTokenStorage('foo'));
        $owner = $factory->createOwner();

        $this->assertEquals($owner, new Owner('foo'));
    }

    public function testE(): void
    {
        $this->expectException(NoTokenFoundException::class);

        $factory = new SymfonyTokenOwnerFactory($this->createTokenStorage('foo', false, false));
        $factory->createOwner();
    }

    public function testE2(): void
    {
        $this->expectException(NoUserFoundException::class);

        $factory = new SymfonyTokenOwnerFactory($this->createTokenStorage('bar', true, false));

        $factory->createOwner();
    }

    /**
     * @return TokenStorageInterface|MockObject
     */
    private function createTokenStorage(string $username, bool $createToken = true, bool $createUser = true)
    {
        $user = $this->createMock(UserInterface::class);
        $user->expects($this->any())
            ->method('getUserIdentifier')
            ->willReturn($username);

        $token = $this->createMock(TokenInterface::class);
        if ($createUser && $createToken) {
            $token->expects($this->once())
                ->method('getUser')
                ->willReturn($user);
        }

        $storage = $this->createMock(TokenStorageInterface::class);
        if ($createToken) {
            $storage->expects($this->once())
                ->method('getToken')
                ->willReturn($token);
        }

        return $storage;
    }
}
