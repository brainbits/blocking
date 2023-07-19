<?php

declare(strict_types=1);

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Tests\Validator;

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Identity\BlockIdentity;
use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Validator\AlwaysInvalidateValidator;
use PHPUnit\Framework\TestCase;

final class AlwaysInvalidateValidatorTest extends TestCase
{
    public function testValidateBlock(): void
    {
        $block = new Block(new BlockIdentity('foo'), new Owner('bar'));

        $validator = new AlwaysInvalidateValidator();
        $this->assertFalse($validator->validate($block));
    }
}
