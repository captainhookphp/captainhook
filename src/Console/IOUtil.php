<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console;

/**
 * IOUtil class
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
abstract class IOUtil
{
    /**
     * Convert a user answer to boolean.
     *
     * @param  string $answer
     * @return string
     */
    public static function answerToBool($answer)
    {
        return in_array($answer, ['y', 'yes', 'ok']);
    }
}
