<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Hook;

use CaptainHook\App\Hooks;
use CaptainHook\App\Runner\Hook;

/**
 *  Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.1.0
 */
class PostCheckout extends Hook
{
    /**
     * The name of the environment variable used to indicate the post-checkout
     * hook should be skipped, to avoid recursion.
     */
    public const SKIP_POST_CHECKOUT_VAR = '_CAPTAIN_HOOK_SKIP_POST_CHECKOUT';

    /**
     * Hook to execute
     *
     * @var string
     */
    protected $hook = Hooks::POST_CHECKOUT;

    /**
     * Runs before any of the hook actions.
     *
     * @return void
     */
    public function beforeHook(): void
    {
        // If this environment variable is set and is `true`, then we want
        // to skip all actions configured by the post-checkout hook.
        $this->skipAllActions = filter_var(
            getenv(self::SKIP_POST_CHECKOUT_VAR),
            FILTER_VALIDATE_BOOLEAN
        );

        parent::beforeHook();
    }
}
