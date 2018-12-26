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
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 3.0.1
 */
final class Hooks
{
    const PRE_COMMIT = 'pre-commit';

    const PRE_PUSH = 'pre-push';

    const COMMIT_MSG = 'commit-msg';

    const PREPARE_COMMIT_MSG = 'prepare-commit-msg';

    const POST_COMMIT = 'post-commit';

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
        ];
    }
}
