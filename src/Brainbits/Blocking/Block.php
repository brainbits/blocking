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
 * Default block
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Block implements BlockInterface
{
    /**
     * @var IdentifierInterface
     */
    protected $identifier;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @param IdentifierInterface $identifier
     * @param OwnerInterface      $owner
     * @param \DateTime           $createdAt
     */
    public function __construct(IdentifierInterface $identifier, OwnerInterface $owner, \DateTime $createdAt)
    {
        $this->identifier = $identifier;
        $this->owner      = $owner;
        $this->createdAt  = $createdAt;
        $this->updatedAt  = $createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}