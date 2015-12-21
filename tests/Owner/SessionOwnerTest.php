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

use Brainbits\Blocking\Owner\SessionOwner;
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
