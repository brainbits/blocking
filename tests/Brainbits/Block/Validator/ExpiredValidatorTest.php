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
 * Expired validator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExpiredValidatorTest extends TestCase
{
    public function testValidateBlockLastUpdatedThirtySecondsAgo()
    {
        $blockMock = $this->getMockBuilder('Brainbits\Blocking\BlockInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->will($this->returnValue(new \DateTime('30 seconds ago')));

        $validator = new ExpiredValidator(20);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new ExpiredValidator(30);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new ExpiredValidator(60);
        $this->assertTrue($validator->validate($blockMock));
    }

    public function testValidateBlockLastUpdatedOneMinuteAgo()
    {
        $blockMock = $this->getMockBuilder('Brainbits\Blocking\BlockInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->will($this->returnValue(new \DateTime('1 minute ago')));

        $validator = new ExpiredValidator(30);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new ExpiredValidator(60);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new ExpiredValidator(90);
        $this->assertTrue($validator->validate($blockMock));
    }

    public function testValidateBlockLastUpdatedOneHourAo()
    {
        $blockMock = $this->getMockBuilder('Brainbits\Blocking\BlockInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->will($this->returnValue(new \DateTime('1 hour ago')));

        $validator = new ExpiredValidator(1800);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new ExpiredValidator(3600);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new ExpiredValidator(5400);
        $this->assertTrue($validator->validate($blockMock));
    }
}