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
use Brainbits\Blocking\Exception\DirectoryNotWritableException;
use Brainbits\Blocking\Exception\FileNotWritableException;

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
        $filename = $this->getFilename($block->getIdentifier());

        if (false === file_put_contents($filename, serialize($block))) {
            throw new \Exception('Write failed');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function touch(BlockInterface $block)
    {
        $filename = $this->getFilename($block->getIdentifier());

        if (false === touch($filename)) {
            throw new \Exception('Touch failed');
        }

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
        if (!$this->exists($block->getIdentifier())) {
            return false;
        }

        $filename = $this->getFilename($block->getIdentifier());
        if (false === unlink($filename)) {
            throw new \Exception('Unlink failed');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function exists(IdentifierInterface $identifier)
    {
        $filename = $this->getFilename($identifier);
        return file_exists($filename);
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
        return $this->ensureFileIsWritable($this->ensureDirectoryExists($this->root) . '/' . $identifier);
    }

    /**
     * Ensure file is writable
     *
     * @param string $file
     * @return string
     * @throws FileNotWritableException
     */
    protected function ensureFileIsWritable($file)
    {
        if (!is_writable(dirname($file))) {
            throw new FileNotWritableException('File ' . $file . ' not writable.');
        }

        return $file;
    }

    /**
     * Ensure directory exists
     *
     * @param string $dirname
     * @return string
     * @throws DirectoryNotWritableException
     */
    protected function ensureDirectoryExists($dirname)
    {
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            throw new DirectoryNotWritableException('Can\'t create block dir ' . $dirname . '.');
        }

        return $dirname;
    }
}
