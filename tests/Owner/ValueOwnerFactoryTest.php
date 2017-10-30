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

use Brainbits\Blocking\Owner\Owner;
use Brainbits\Blocking\Owner\ValueOwnerFactory;
use PHPUnit\Framework\TestCase;

/**
 * Value owner test
 */
class ValueOwnerFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new ValueOwnerFactory('foo');
        $owner = $factory->createOwner();

        $this->assertEquals($owner, new Owner('foo'));
    }
}
