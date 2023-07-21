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

namespace Brainbits\Blocking\Bundle\Controller;

use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\Identity\BlockIdentity;
use Symfony\Component\HttpFoundation\JsonResponse;

final class BlockingController
{
    public function __construct(private Blocker $blocker)
    {
    }

    public function blockAction(string $identifier): JsonResponse
    {
        $identity = new BlockIdentity($identifier);

        $block = $this->blocker->tryBlock($identity);

        return new JsonResponse([
            'success' => !!$block,
        ]);
    }

    public function unblockAction(string $identifier): JsonResponse
    {
        $identity = new BlockIdentity($identifier);

        $block = $this->blocker->unblock($identity);

        return new JsonResponse([
            'success' => !!$block,
        ]);
    }
}
