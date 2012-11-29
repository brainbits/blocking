<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Adapter;

use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\BlockInterface;

/**
 * Block adapter interface
 *
 * @author  Stephan Wentz <sw@brainbits.net>
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