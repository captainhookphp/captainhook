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
use CaptainHook\App\Hook\Action;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

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
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $processor = new Processor();
        $result    = $processor->run($action->getAction());

        if (!$result->isSuccessful()) {
            throw Exception\ActionFailed::withMessage($result->getStdOut() . PHP_EOL . $result->getStdErr());
        }

        $io->write($result->getStdOut());
    }
}
