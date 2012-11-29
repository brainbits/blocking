<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Owner;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Session owner
 *
 * @author  Stephan Wentz <sw@brainbits.net>
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