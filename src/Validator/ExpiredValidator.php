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

namespace Brainbits\Blocking\Validator;

use Brainbits\Blocking\BlockInterface;
use DateInterval;
use DateTimeImmutable;

/**
 * Expired validator.
 * Checks if a block is expired.
 */
class ExpiredValidator implements ValidatorInterface
{
    public function __construct(private int $expireSeconds)
    {
    }

    public function validate(BlockInterface $block): bool
    {
        $now = new DateTimeImmutable();
        $updatedAt = $block->getUpdatedAt();

        $interval = $updatedAt->diff($now);
        $diffInSeconds = $this->intervalToSeconds($interval);

        return $this->expireSeconds > $diffInSeconds;
    }

    /**
     * Calculate seconds from interval
     */
    private function intervalToSeconds(DateInterval $interval): int
    {
        $seconds = (int) $interval->format('%s');

        $multiplier = 60;
        $seconds += (int) $interval->format('%i') * $multiplier;

        $multiplier *= 60;
        $seconds += (int) $interval->format('%h') * $multiplier;

        $multiplier *= 24;
        $seconds += (int) $interval->format('%d') * $multiplier;

        $multiplier *= 30;
        $seconds += (int) $interval->format('%m') * $multiplier;

        $multiplier *= 12;

        return $seconds + (int) $interval->format('%y') * $multiplier;
    }
}
