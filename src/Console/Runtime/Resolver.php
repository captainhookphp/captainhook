<?php

/**
 * This file is part of captainhook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\Runtime;

/**
 * Class Resolver
 *
 * @package CaptainHook\App
 */
class Resolver
{
    /**
     * PHAR flag, replaced by box during PHAR building
     *
     * @var bool
     */
    private $runtime = '@runtime@';

    /**
     * Check if the current runtime is executed via PHAR
     *
     * @return bool
     */
    public function isPharRelease(): bool
    {
        return $this->runtime === 'PHAR';
    }
}
