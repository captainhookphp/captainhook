<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook\Message\Validator;

use HookMeUp\Git\CommitMessage;

/**
 * Interface Rule
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
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
     * @param  \HookMeUp\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg);
}
