<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Hook\Message\Validator\Rule;

use HookMeUp\App\Git\CommitMessage;
use HookMeUp\App\Hook\Message\Validator\Rule;

/**
 * Class Base
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
abstract class Base implements Rule
{
    /**
     * Rule hint.
     *
     * @var string
     */
    protected $hint;

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @param  \HookMeUp\App\Git\CommitMessage $msg
     * @return bool
     */
    abstract public function pass(CommitMessage $msg);
}
