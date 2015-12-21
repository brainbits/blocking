<?php

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Validator;

use Brainbits\Blocking\BlockInterface;

/**
 * Expired validator
 * Checks if a block is expired
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExpiredValidator implements ValidatorInterface
{
    /**
     * @var integer
     */
    protected $expire;

    /**
     * @param integer $expire
     */
    public function __construct($expire)
    {
        $this->expire = (integer)$expire;
    }

    /**
     * @inheritDoc
     */
    public function validate(BlockInterface $block)
    {
        $now = new \DateTime();
        $updatedAt = $block->getUpdatedAt();

        $interval = $updatedAt->diff($now);
        $diffInSeconds = $this->intervalToSeconds($interval);

        return $this->expire > $diffInSeconds;
    }

    /**
     * Calculate seconds from interval
     *
     * @param \DateInterval $interval
     * @return integer
     */
    private function intervalToSeconds(\DateInterval $interval)
    {
        $seconds = $interval->format('%s');

        $multiplier = 60;
        $seconds += $interval->format('%i') * $multiplier;

        $multiplier *= 60;
        $seconds += $interval->format('%h') * $multiplier;

        $multiplier *= 24;
        $seconds += $interval->format('%d') * $multiplier;

        $multiplier *= 30;
        $seconds += $interval->format('%m') * $multiplier;

        $multiplier *= 12;
        $seconds += $interval->format('%y') * $multiplier;

        return $seconds;
    }
}
