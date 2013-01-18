<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking;

use Brainbits\Blocking\Adapter\AdapterInterface;
use Brainbits\Blocking\Validator\ValidatorInterface;
use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\Owner\OwnerInterface;

/**
 * Blocker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Blocker
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var OwnerInterface
     */
    protected $owner;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param AdapterInterface   $adapter
     * @param OwnerInterface     $owner
     * @param ValidatorInterface $validator
     */
    public function __construct(AdapterInterface $adapter, OwnerInterface $owner, ValidatorInterface $validator)
    {
        $this->adapter   = $adapter;
        $this->owner     = $owner;
        $this->validator = $validator;
    }

    /**
     * Set block
     *
     * @param IdentifierInterface $identifier
     * @return BlockInterface
     */
    public function block(IdentifierInterface $identifier)
    {
        if ($this->isBlocked($identifier)) {
            $block = $this->getBlock($identifier);
            if ((string)$block->getOwner() !== (string)$this->owner) {
                throw new \Exception('Already blocked');
            }
            $this->adapter->touch($block);
            return $block;
        }

        $block = new Block($identifier, $this->owner, new \DateTime());

        $this->adapter->write($block);

        return $block;
    }


    /**
     * Remove block
     *
     * @param IdentifierInterface $identifier
     * @return boolean
     */
    public function unblock(IdentifierInterface $identifier)
    {
        $block = $this->getBlock($identifier);
        if (null === $block) {
            return false;
        }

        $this->adapter->remove($block);

        return true;
    }

    /**
     * Is a block set?
     *
     * @param IdentifierInterface $identifier
     * @return boolean
     */
    public function isBlocked(IdentifierInterface $identifier)
    {
        $exists = $this->adapter->exists($identifier);

        if (!$exists) {
            return false;
        }

        $block = $this->adapter->get($identifier);
        $valid = $this->validator->validate($block);

        if ($valid) {
            return true;
        }

        $this->adapter->remove($block);

        return false;
    }

    /**
     * Return block
     *
     * @param IdentifierInterface $identifier
     * @return BlockInterface|null
     */
    public function getBlock(IdentifierInterface $identifier)
    {
        if (!$this->isBlocked($identifier)) {
            return null;
        }

        return $this->adapter->get($identifier);
    }
}