<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp;

use HookMeUp\Console\Application;

/**
 * Class Cmd
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Cmd
{
    /**
     * Execute the main application.
     */
    public static function main()
    {
        $app = new Application\Main();
        $app->run();
    }

    /**
     * Execute a hook.
     *
     * @param string $hook
     * @param string $config
     */
    public static function hook($hook, $config = null)
    {
        $app = new Application\Hook();
        $app->executeHook($hook)
            ->useConfigFile($config)
            ->run();
    }
}
