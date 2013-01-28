<?php
/**
 * This file is part of the brainbits block package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
 */

namespace Brainbits\Blocking\Adapter;

use Brainbits\Blocking\Identifier\IdentifierInterface;
use Brainbits\Blocking\BlockInterface;
use Brainbits\Blocking\Block;

/**
 * Filesystem block adapter
 * Uses files for storing block information
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilesystemAdapter implements AdapterInterface
{
    /**
     * @var string
     */
    protected $root;

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * @inheritDoc
     */
    public function write(BlockInterface $block)
    {
        file_put_contents(
            $this->getFilename($block->getIdentifier()),
            serialize($block)
        );

        return true;
    }

    /**
     * @inheritDoc
     */
    public function touch(BlockInterface $block)
    {
        $filename = $this->getFilename($block->getIdentifier());
        touch($filename);
        $updatedAt = new \DateTime();
        $updatedAt->setTimestamp(filemtime($filename));
        $block->setUpdatedAt($updatedAt);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove(BlockInterface $block)
    {
        if (!$this->exists($block->getIdentifier()))
        {
            return false;
        }

        unlink($this->getFilename($block->getIdentifier()));

        return true;
    }

    /**
     * @inheritDoc
     */
    public function exists(IdentifierInterface $identifier)
    {
        return file_exists($this->getFilename($identifier));
    }

    /**
     * @inheritDoc
     */
    public function get(IdentifierInterface $identifier)
    {
        if (!$this->exists($identifier)) {
            return null;
        }

        $filename = $this->getFilename($identifier);
        $content = file_get_contents($filename);
        $updatedAt = new \DateTime();
        $updatedAt->setTimestamp(filemtime($filename));
        $block = unserialize($content);
        $block->setUpdatedAt($updatedAt);

        return $block;
    }

    /**
     * Return assembled filename
     *
     * @param IdentifierInterface $identifier
     * @return string
     */
    protected function getFilename(IdentifierInterface $identifier)
    {
        return $this->ensureDirectoryExists($this->root) . '/' . $identifier;
    }

    /**
     * Ensure directory exists
     *
     * @param string $filename
     * @return string
     * @throws \Exception
     */
    protected function ensureDirectoryExists($dirname)
    {
        if (!file_exists($dirname)) {
            if (!mkdir($dirname, 0777, true)) {
                throw new \Exception('Can\'t create block dir ' . $dirname);
            }
        }

        return $dirname;
    }
}