<?php declare(strict_types=1);
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App;

/**
 * Class Hooks
 *
 * Defines the list of hooks that can be handled with captainhook and provides some name constants.
 *
 * @package CaptainHook
 * @author  Andrea Heigl <andreas@heigl.org>
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

    /**
     * Returns the list of valid hooks
     *
     * @return array
     */
    public static function getValidHooks() : array
    {
        return [
            self::COMMIT_MSG         => 'CommitMsg',
            self::PRE_PUSH           => 'PrePush',
            self::PRE_COMMIT         => 'PreCommit',
            self::PREPARE_COMMIT_MSG => 'PrepareCommitMsg',
            self::POST_COMMIT        => 'PostCommit',
            self::POST_MERGE         => 'PostMerge',
            self::POST_CHECKOUT      => 'PostCheckout',
        ];
    }
}
