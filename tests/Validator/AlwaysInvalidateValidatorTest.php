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
use Brainbits\Blocking\Validator\AlwaysInvalidateValidator;
use PHPUnit\Framework\TestCase;

/**
 * Always invalidate validator test
 */
class AlwaysInvalidateValidatorTest extends TestCase
{
    public function testValidateBlockLastUpdatedThirtySecondsAgo()
    {
        $blockMock = $this->prophesize(BlockInterface::class);
        $blockMock->getUpdatedAt()->willReturn(new \DateTime('30 seconds ago'));

        $validator = new AlwaysInvalidateValidator(20);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new AlwaysInvalidateValidator(30);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new AlwaysInvalidateValidator(60);
        $this->assertFalse($validator->validate($blockMock->reveal()));
    }

    public function testValidateBlockLastUpdatedOneMinuteAgo()
    {
        $blockMock = $this->prophesize(BlockInterface::class);
        $blockMock->getUpdatedAt()->willReturn(new \DateTime('1 minute ago'));

        $validator = new AlwaysInvalidateValidator(30);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new AlwaysInvalidateValidator(60);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new AlwaysInvalidateValidator(90);
        $this->assertFalse($validator->validate($blockMock->reveal()));
    }

    public function testValidateBlockLastUpdatedOneHourAo()
    {
        $blockMock = $this->prophesize(BlockInterface::class);
        $blockMock->getUpdatedAt()->willReturn(new \DateTime('1 hour ago'));

        $validator = new AlwaysInvalidateValidator(1800);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new AlwaysInvalidateValidator(3600);
        $this->assertFalse($validator->validate($blockMock->reveal()));

        $validator = new AlwaysInvalidateValidator(5400);
        $this->assertFalse($validator->validate($blockMock->reveal()));
    }
}
