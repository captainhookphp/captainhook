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

namespace CaptainHook\App;

use RuntimeException;

/**
 * Class Hooks
 *
 * Defines the list of hooks that can be handled with captainhook and provides some name constants.
 *
 * @package CaptainHook
 * @author  Andrea Heigl <andreas@heigl.org>
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 3.0.1
 */
final class Hooks
{
    public const PRE_COMMIT = 'pre-commit';

    public const PRE_PUSH = 'pre-push';

    public const COMMIT_MSG = 'commit-msg';

    public const PREPARE_COMMIT_MSG = 'prepare-commit-msg';

    public const POST_COMMIT = 'post-commit';

    public const POST_MERGE = 'post-merge';

    public const POST_CHECKOUT = 'post-checkout';

    public const POST_REWRITE = 'post-rewrite';

    public const POST_CHANGE = 'post-change';

    /**
     * This defines which native hook trigger which virtual hook
     *
     * @var string[]
     */
    private static $virtualHookTriggers = [
        self::POST_CHECKOUT => self::POST_CHANGE,
        self::POST_MERGE    => self::POST_CHANGE,
        self::POST_REWRITE  => self::POST_CHANGE,
    ];

    /**
     * Returns the list of valid hooks
     *
     * @return array<string, string>
     */
    public static function getValidHooks(): array
    {
        return array_merge(self::nativeHooks(), self::virtualHooks());
    }

    /**
     * Returns a list of all natively supported git hooks
     *
     * @return array<string, string>
     */
    public static function nativeHooks(): array
    {
        return [
            self::COMMIT_MSG         => 'CommitMsg',
            self::PRE_PUSH           => 'PrePush',
            self::PRE_COMMIT         => 'PreCommit',
            self::PREPARE_COMMIT_MSG => 'PrepareCommitMsg',
            self::POST_COMMIT        => 'PostCommit',
            self::POST_MERGE         => 'PostMerge',
            self::POST_CHECKOUT      => 'PostCheckout',
            self::POST_REWRITE       => 'PostRewrite'
        ];
    }

    /**
     * Return a list of all artificial CaptainHook hooks (virtual hooks)
     *
     * @return array<string, string>
     */
    public static function virtualHooks(): array
    {
        return [
            self::POST_CHANGE => 'PostChange'
        ];
    }

    /**
     * Returns a list of all native hooks triggered by a given virtual hook
     *
     * @return array<string>
     */
    public static function getNativeHooksForVirtualHook(string $virtualHook): array
    {
        return array_keys(
            array_filter(
                self::$virtualHookTriggers,
                function ($e) use ($virtualHook) {
                    return $e === $virtualHook;
                }
            )
        );
    }

    /**
     * Returns the argument placeholders for a given hook
     *
     * @param  string $hook
     * @return string
     */
    public static function getOriginalHookArguments(string $hook): string
    {
        static $arguments = [
            Hooks::COMMIT_MSG         => ' {$FILE}',
            Hooks::POST_MERGE         => ' {$SQUASH}',
            Hooks::PRE_COMMIT         => '',
            Hooks::PRE_PUSH           => ' {$TARGET} {$URL}',
            Hooks::PREPARE_COMMIT_MSG => ' {$FILE} {$MODE} {$HASH}',
            Hooks::POST_CHECKOUT      => ' {$PREVIOUSHEAD} {$NEWHEAD} {$MODE}',
            Hooks::POST_REWRITE       => ' {$GIT-COMMAND}',
        ];

        return $arguments[$hook];
    }

    /**
     * Tell if the given hook should trigger a virtual hook
     *
     * @param  string $hook
     * @return bool
     */
    public static function triggersVirtualHook(string $hook): bool
    {
        return isset(self::$virtualHookTriggers[$hook]);
    }

    /**
     * Return the virtual hook name that should be triggered by given hook
     *
     * @param  string $hook
     * @return string
     */
    public static function getVirtualHook(string $hook): string
    {
        if (!self::triggersVirtualHook($hook)) {
            throw new RuntimeException('no virtual hooks for ' . $hook);
        }
        return self::$virtualHookTriggers[$hook];
    }
}
