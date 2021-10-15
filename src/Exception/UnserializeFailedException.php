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

/**
 * Unserialize failed exception.
 */
final class UnserializeFailedException extends RuntimeException
{
    private string $input;

    private function __construct(string $message, string $input)
    {
        parent::__construct($message);

        $this->input = $input;
    }

    public static function createFromInput(mixed $input): self
    {
        return new self('Unserialize failed.', (string) $input);
    }

    public function getInput(): string
    {
        return $this->input;
    }
}
