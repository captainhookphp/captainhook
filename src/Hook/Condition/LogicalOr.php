<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Condition;

use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Condition;
use SebastianFeldmann\Git\Repository;

/**
 * This class needs to be named like this as a simple "Or" is not allowed as class name
 */
final class LogicalOr implements Condition
{
    use LogicalTrait;

    public function isTrue(IO $io, Repository $repository): bool
    {
        foreach ($this->conditions as $condition) {
            if (true === $condition->isTrue($io, $repository)) {
                return true;
            }
        }
        return false;
    }
}
