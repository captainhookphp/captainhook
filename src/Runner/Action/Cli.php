<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception;
use CaptainHook\App\Git\Repository;
use CaptainHook\App\Hook\Action;
use Symfony\Component\Process\Process;

/**
 * Class Cli
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Cli implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\App\Config         $config
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \CaptainHook\App\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \CaptainHook\App\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $process = new Process($action->getAction());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new Exception\ActionExecution($process->getOutput() . PHP_EOL . $process->getErrorOutput());
        }

        $io->write($process->getOutput());
    }
}
