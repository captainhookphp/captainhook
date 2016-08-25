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
 * Class SeparateSubjectFromBodyWithBlankLine
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class SeparateSubjectFromBodyWithBlankLine extends Base
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hint = 'Subject and body have to be separated by a blank line';
    }

    /**
     * Check if subject and body are separated by a blank line.
     *
     * @param  \HookMeUp\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg)
    {
        return $msg->getLineCount() < 2 || empty($msg->getLine(1));
    }
}
