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

namespace Brainbits\Blocking\Exception;

use function gettype;
use function is_scalar;

/**
 * Unserialize failed exception.
 */
final class UnserializeFailedException extends RuntimeException
{
    private function __construct(string $message, private string $input)
    {
        parent::__construct($message);
    }

    public static function createFromInput(mixed $input): self
    {
        return new self('Unserialize failed.', is_scalar($input) ? (string) $input : gettype($input));
    }

    public function getInput(): string
    {
        return $this->input;
    }
}
