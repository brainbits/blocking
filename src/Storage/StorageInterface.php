<?php

declare(strict_types = 1);

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Storage;

use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Identifier\IdentifierInterface;

/**
 * Block storage interface.
 */
interface StorageInterface
{
    public function write(BlockInterface $block): bool;

    public function touch(BlockInterface $block): bool;

    public function remove(BlockInterface $block): bool;

    public function exists(IdentifierInterface $identifier): bool;

    public function get(IdentifierInterface $identifier): ?BlockInterface;
}
