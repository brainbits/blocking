<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * (c) 2012-2013 brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Adapter;

use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\BlockInterface;

/**
 * Block adapter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface AdapterInterface
{
    /**
     * Write block
     *
     * @param BlockInterface $block
     * @return boolean
     */
    public function write(BlockInterface $block);

    /**
     * Touch block
     *
     * @param BlockInterface $block
     * @return boolean
     */
    public function touch(BlockInterface $block);

    /**
     * Remove block
     *
     * @param BlockInterface $block
     * @return boolean
     */
    public function remove(BlockInterface $block);

    /**
     * Is a block set?
     *
     * @param BlockInterface $block
     * @return boolean
     */
    public function exists(IdentifierInterface $identifier);

    /**
     * Return block
     *
     * @param BlockInterface $block
     * @return BlockInterface
     */
    public function get(IdentifierInterface $identifier);
}
