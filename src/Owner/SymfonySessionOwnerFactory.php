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

use Brainbits\Blocking\Exception\NoSessionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Symfony session owner factory.
 */
class SymfonySessionOwnerFactory implements OwnerFactoryInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function createOwner(): OwnerInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            throw NoSessionException::create();
        }

        $session = $request->getSession();
        if (!$session instanceof SessionInterface) {
            throw NoSessionException::create();
        }

        return new Owner($session->getId());
    }
}
