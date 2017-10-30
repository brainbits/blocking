<?php

declare(strict_types = 1);

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Owner;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Symfony session owner factory.
 */
class SymfonySessionOwnerFactory implements OwnerFactoryInterface
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function createOwner(): OwnerInterface
    {
        return new Owner($this->session->getId());
    }

    public function __toString(): string
    {
        return $this->session->getId();
    }
}
