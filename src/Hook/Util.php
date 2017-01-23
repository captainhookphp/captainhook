<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook;

/**
 * Class Util
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
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
    public static function isValid(string $hook) : bool
    {
        return isset(self::$validHooks[$hook]);
    }

    /**
     * Return valid hooks.
     *
     * @return array
     */
    public static function getValidHooks() : array
    {
        return self::$validHooks;
    }

    /**
     * Get a list of all hooks.
     *
     * @return array
     */
    public static function getHooks() : array
    {
        return array_keys(self::$validHooks);
    }

    /**
     * Return action type.
     *
     * @param  string $action
     * @return string
     */
    public static function getActionType(string $action) : string
    {
        return substr($action, 0, 1) === '\\' ? 'php' : 'cli';
    }
}
