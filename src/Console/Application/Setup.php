<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Application;

use CaptainHook\App\Console\Application;
use CaptainHook\App\Console\Command as Cmd;

/**
 * Class Setup
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Setup extends Application
{
    /**
     * Initializes all the CaptainHook commands.
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands() : array
    {
        return [
            new Cmd\Help(),
            new Cmd\Install(),
            new Cmd\Configuration(),
            new Cmd\Add(),
            new Cmd\Disable(),
            new Cmd\Enable(),
        ];
    }
}
