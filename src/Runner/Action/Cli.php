<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Runner\Action;

use CaptainHook\Config;
use CaptainHook\Console\IO;
use CaptainHook\Exception;
use CaptainHook\Git\Repository;
use CaptainHook\Hook\Action;
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
     * @param  \CaptainHook\Config         $config
     * @param  \CaptainHook\Console\IO     $io
     * @param  \CaptainHook\Git\Repository $repository
     * @param  \CaptainHook\Config\Action  $action
     * @throws \CaptainHook\Exception\ActionExecution
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
