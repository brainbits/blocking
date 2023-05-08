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
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AlwaysInvalidateValidatorTest extends TestCase
{
    public function testValidateBlockLastUpdatedThirtySecondsAgo(): void
    {
        $blockMock = $this->createMock(BlockInterface::class);
        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->willReturn(new DateTimeImmutable('30 seconds ago'));

        $validator = new AlwaysInvalidateValidator(20);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(30);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(60);
        $this->assertFalse($validator->validate($blockMock));
    }

    public function testValidateBlockLastUpdatedOneMinuteAgo(): void
    {
        $blockMock = $this->createMock(BlockInterface::class);
        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->willReturn(new DateTimeImmutable('1 minute ago'));

        $validator = new AlwaysInvalidateValidator(30);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(60);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(90);
        $this->assertFalse($validator->validate($blockMock));
    }

    public function testValidateBlockLastUpdatedOneHourAo(): void
    {
        $blockMock = $this->createMock(BlockInterface::class);
        $blockMock->expects($this->any())
            ->method('getUpdatedAt')
            ->willReturn(new DateTimeImmutable('1 hour ago'));

        $validator = new AlwaysInvalidateValidator(1800);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(3600);
        $this->assertFalse($validator->validate($blockMock));

        $validator = new AlwaysInvalidateValidator(5400);
        $this->assertFalse($validator->validate($blockMock));
    }
}
