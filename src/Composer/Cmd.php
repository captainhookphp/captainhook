<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Composer;

use Composer\Script\Event;

/**
 * Class Cmd
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
abstract class Cmd
{
    public static function configure(Event $event, $config = null)
    {
        $app = new Application();
        $app->setAutoExit(false);
        $app->setConfigFile($config);
        $app->setProxyIO($event->getIO());
        $app->run();
    }
}
