<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message;

use sebastianfeldmann\CaptainHook\Git\CommitMessage;

/**
 * Interface Rule
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
interface Rule
{
    /**
     * Return a hint how to pass the rule.
     *
     * @return string
     */
    public function getHint();

    /**
     * Checks if a commit message passes the rule.
     *
     * @param  \sebastianfeldmann\CaptainHook\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg);
}
