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

final readonly class SymfonySessionOwnerFactory implements OwnerFactoryInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function createOwner(): Owner
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            throw NoSessionException::create();
        }

        $session = $request->getSession();

        return new Owner($session->getId());
    }
}
