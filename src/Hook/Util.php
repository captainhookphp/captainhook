<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook;

/**
 * Class Util
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
abstract class Util
{
    /**
     * All valid hooks
     *
     * @var array
     */
    private static $validHooks = ['commit-msg' => 1, 'pre-commit' => 1, 'pre-push' => 1];

    /**
     * Checks if a hook name is valid.
     *
     * @param  string $hook
     * @return bool
     */
    public static function isValid($hook)
    {
        return isset(self::$validHooks[$hook]);
    }

    /**
     * Return valid hooks.
     *
     * @return array
     */
    public static function getValidHooks()
    {
        return self::$validHooks;
    }

    /**
     * Get a list of all hooks.
     *
     * @return array
     */
    public static function getHooks()
    {
        return array_keys(self::$validHooks);
    }

    /**
     * Return action type.
     *
     * @param  string $action
     * @return string
     */
    public static function getActionType($action)
    {
        return substr($action, 0, 1) === '\\' ? 'php' : 'cli';
    }
}
