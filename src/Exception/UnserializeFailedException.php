<?php

declare(strict_types = 1);

/*
 * This file is part of the brainbits blocking package.
 *
 * (c) brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brainbits\Blocking\Exception;

use Throwable;

/**
 * Unserialize failed exception.
 */
final class UnserializeFailedException extends RuntimeException
{
    private $input;

    public function __construct(string $message, ?int $code, ?Throwable $previous, string $input)
    {
        parent::__construct($message, $code, $previous);

        $this->input = $input;
    }

    /**
     * @param mixed $input
     */
    public static function createFromInput($input): self
    {
        return new self("Unserialize failed.", null, null, (string) $input);
    }

    public function getInput(): string
    {
        return $this->input;
    }
}
