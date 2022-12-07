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

namespace Brainbits\Blocking\Owner;

use Brainbits\Blocking\Exception\NoTokenFoundException;
use Brainbits\Blocking\Exception\NoUserFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Symfony token owner.
 */
class SymfonyTokenOwnerFactory implements OwnerFactoryInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function createOwner(): OwnerInterface
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            throw NoTokenFoundException::create();
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            throw NoUserFoundException::create();
        }

        return new Owner($user->getUserIdentifier());
    }
}
