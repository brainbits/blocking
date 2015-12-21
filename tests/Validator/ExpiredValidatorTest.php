<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests\Validator;

use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Validator\ExpiredValidator;
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
        $blockMock = $this->prophesize(BlockInterface::class);
        $blockMock->getUpdatedAt()->willReturn(new \DateTime('30 seconds ago'));

        $validator = new ExpiredValidator(20);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new ExpiredValidator(30);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new ExpiredValidator(60);
        $this->assertTrue($validator->validate($blockMock->reveal()));
    }

    public function testValidateBlockLastUpdatedOneMinuteAgo()
    {
        $blockMock = $this->prophesize(BlockInterface::class);
        $blockMock->getUpdatedAt()->willReturn(new \DateTime('1 minute ago'));

        $validator = new ExpiredValidator(30);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new ExpiredValidator(60);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new ExpiredValidator(90);
        $this->assertTrue($validator->validate($blockMock->reveal()));
    }

    public function testValidateBlockLastUpdatedOneHourAo()
    {
        $blockMock = $this->prophesize(BlockInterface::class);
        $blockMock->getUpdatedAt()->willReturn(new \DateTime('1 hour ago'));

        $validator = new ExpiredValidator(1800);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new ExpiredValidator(3600);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new ExpiredValidator(5400);
        $this->assertTrue($validator->validate($blockMock->reveal()));
    }
}
