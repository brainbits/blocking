<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * (c) 2012-2013 brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Validator;

use Brainbits\Blocking\BlockInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Always invalidate validator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AlwaysInvalidateValidatorTest extends TestCase
{
    public function testValidateBlockLastUpdatedThirtySecondsAgo()
    {
        $blockMock = $this->getMockBuilder('Brainbits\Blocking\BlockInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->will($this->returnValue(new \DateTime('30 seconds ago')));

        $validator = new AlwaysInvalidateValidator(20);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(30);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(60);
        $this->assertFalse($validator->validate($blockMock));
    }

    public function testValidateBlockLastUpdatedOneMinuteAgo()
    {
        $blockMock = $this->getMockBuilder('Brainbits\Blocking\BlockInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->will($this->returnValue(new \DateTime('1 minute ago')));

        $validator = new AlwaysInvalidateValidator(30);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(60);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(90);
        $this->assertFalse($validator->validate($blockMock));
    }

    public function testValidateBlockLastUpdatedOneHourAo()
    {
        $blockMock = $this->getMockBuilder('Brainbits\Blocking\BlockInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->will($this->returnValue(new \DateTime('1 hour ago')));

        $validator = new AlwaysInvalidateValidator(1800);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(3600);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(5400);
        $this->assertFalse($validator->validate($blockMock));
    }
}