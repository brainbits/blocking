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
 * Symfony session owner.
 */
class SymfonySessionOwner implements OwnerInterface
{
    /**
     * @var string
     */
    private $sessionId;

    public function __construct(SessionInterface $session)
    {
        $this->sessionId = $session->getId();
    }

    public function equals(OwnerInterface $owner): bool
    {
        return (string) $this === (string) $owner;
    }

    public function __toString(): string
    {
        return $this->sessionId;
    }
}
