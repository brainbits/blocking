<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Owner;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Session owner test
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class SessionOwnerTest extends TestCase
{
    public function testToString()
    {
        $sessionMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\SessionInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $sessionMock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('abcdef'));

        $owner = new SessionOwner($sessionMock);

        $this->assertEquals('abcdef', (string)$owner);
    }
}