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

namespace Brainbits\Blocking\Storage;

use Brainbits\Blocking\Block;
use Brainbits\Blocking\Identity\BlockIdentity;

/**
 * Block storage interface.
 */
interface StorageInterface
{
    public function write(Block $block, int $ttl): bool;

    public function touch(Block $block, int $ttl): bool;

    public function remove(Block $block): bool;

    public function exists(BlockIdentity $identity): bool;

    public function get(BlockIdentity $identity): Block|null;
}
