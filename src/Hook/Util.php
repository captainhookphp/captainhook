<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook;

use CaptainHook\App\Hooks;
use RuntimeException;

/**
 * Class Util
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Util
{
    /**
     * Checks if a hook name is valid
     *
     * @param  string $hook
     * @return bool
     */
    public static function isValid(string $hook) : bool
    {
        return isset(Hooks::getValidHooks()[$hook]);
    }

    /**
     * Returns list of valid hooks
     *
     * @return array
     */
    public static function getValidHooks() : array
    {
        return Hooks::getValidHooks();
    }

    /**
     * Returns hooks command class
     *
     * @param  string $hook
     * @return string
     */
    public static function getHookCommand(string $hook) : string
    {
        if (!self::isValid($hook)) {
            throw new RuntimeException(sprintf('Hook \'%s\' is not supported', $hook));
        }
        return Hooks::getValidHooks()[$hook];
    }

    /**
     * Get a list of all supported hooks
     *
     * @return array
     */
    public static function getHooks() : array
    {
        return array_keys(Hooks::getValidHooks());
    }
}
