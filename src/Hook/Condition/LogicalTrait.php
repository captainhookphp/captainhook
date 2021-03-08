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
 * This class needs to be named like this as a simple "And" is not allowed as class name
 */
trait LogicalTrait
{
    /** @var Condition[] */
    private $conditions = [];

    private function __construct(Condition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    public static function fromConditionsArray(array $conditions): Condition
    {
        $realConditions = [];
        foreach ($conditions as $condition) {
            if (! $condition instanceof Condition) {
                continue;
            }

            $realConditions[] = $condition;
        }

        return new static(...$realConditions);
    }
}
