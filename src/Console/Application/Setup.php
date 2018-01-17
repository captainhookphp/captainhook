<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console\Application;

use SebastianFeldmann\CaptainHook\Console\Application;
use SebastianFeldmann\CaptainHook\Console\Command as Cmd;

/**
 * Class Setup
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Setup extends Application
{
    /**
     * Initializes all the CaptainHook commands.
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands()
    {
        return [
            new Cmd\Help(),
            new Cmd\Configuration(),
            new Cmd\Install(),
            new Cmd\Run(),
        ];
    }
}
