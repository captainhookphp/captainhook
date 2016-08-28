<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Application;

use HookMeUp\Console\Application;
use HookMeUp\Console\Command as Cmd;

/**
 * Class Main
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Main extends Application
{
    /**
     * Initializes all the hookmeup commands.
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands()
    {
        $commands = array_merge(
            parent::getDefaultCommands(),
            [
                new Cmd\Configuration(),
                new Cmd\Install(),
                new Cmd\Run(),
            ]
        );
        return $commands;
    }
}
