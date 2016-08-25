<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook\Message\Validator\Rule;

use HookMeUp\Git\CommitMessage;

/**
 * Class CapitdalizeSubject
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class CapitalizeSubject extends Base
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hint = 'Subject line has to start with an upper case letter';
    }

    /**
     * Check if commit message starts with upper case letter.
     *
     * @param  \HookMeUp\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg)
    {
        if (!$msg->isEmpty()) {
            $firstLetter = substr($msg->getSubject(), 0, 1);
            return $firstLetter === strtoupper($firstLetter);
        }
        return false;
    }
}
