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
 * Class NoPeriodOnSubjectEnd
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class NoPeriodOnSubjectEnd extends Base
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hint = 'Subject should not end with a period.';
    }

    /**
     * Check if commit message doesn't end with a period.
     *
     * @param  \HookMeUp\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg)
    {
        return substr(trim($msg->getSubject()), -1) !== '.';
    }
}
