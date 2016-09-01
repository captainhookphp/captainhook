<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message\Rule;

use sebastianfeldmann\CaptainHook\Git\CommitMessage;

/**
 * Class CapitdalizeSubject
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
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
     * @param  \sebastianfeldmann\CaptainHook\Git\CommitMessage $msg
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
