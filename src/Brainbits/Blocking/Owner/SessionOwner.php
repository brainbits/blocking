<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * (c) 2012-2013 brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Owner;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Session owner
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SessionOwner implements OwnerInterface
{
    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->sessionId = $session->getId();
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->sessionId;
    }
}
