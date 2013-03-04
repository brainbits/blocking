<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking;

use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\Owner\OwnerInterface;

/**
 * Block interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface BlockInterface
{
    /**
     * Return identifier
     *
     * @return IdentifierInterface
     */
    public function getIdentifier();

    /**
     * Return owner
     *
     * @return OwnerInterface
     */
    public function getOwner();

    /**
     * Set created at
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updated at
     *
     * @param \DateTime $updatedAt
     * @returns BlockInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Return modified at
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}