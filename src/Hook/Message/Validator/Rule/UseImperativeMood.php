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
 * Class UseImperativeMood
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class UseImperativeMood extends Base
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->hint = 'Subject should be written in imperative mood';
    }

    /**
     * Check if commit message is written in imperative mood.
     *
     * @param  \HookMeUp\Git\CommitMessage $msg
     * @return bool
     */
    public function pass(CommitMessage $msg)
    {
        $lowerSubject = strtolower($msg->getSubject());
        $blackList    = [
            'uploaded',
            'updated',
            'added',
            'created',
        ];
        foreach ($blackList as $term) {
            if (strpos($lowerSubject, $term) !== false) {
                $this->hint .= PHP_EOL . 'Invalid use of \'' . $term . '\'';
                return false;
            }
        }
        return true;
    }
}
