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
 * Class LimitSubjectLength
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class LimitSubjectLength extends Base
{
    /**
     * Length limit
     *
     * @var int
     */
    protected $maxLength;

    /**
     * Constructor.
     *
     * @param int $length
     */
    public function __construct($length = 50)
    {
        $this->hint      = 'Subject line should not exceed ' . $length . ' characters';
        $this->maxLength = $length;
    }

    /**
     * Check if commit message doesn't exceeed the max length.
     *
     * @param  \HookMeUp\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg)
    {
        return strlen(($msg->getSubject())) <= $this->maxLength;
    }
}
