<?php

declare(strict_types=1);

/*
 * This file is part of the brainbits blocking bundle package.
 *
 * (c) brainbits GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Bundle\Tests;

use Brainbits\Blocking\Bundle\BrainbitsBlockingBundle;
use PHPUnit\Framework\TestCase;

final class BrainbitsBlockingBundleTest extends TestCase
{
    public function testConstructor(): void
    {
        $bundle = new BrainbitsBlockingBundle();

        $this->assertInstanceOf(BrainbitsBlockingBundle::class, $bundle);
    }
}
