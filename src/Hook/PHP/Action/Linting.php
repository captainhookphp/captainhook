<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\PHP\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

/**
 * Class Linter
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 1.0.5
 */
class Linting implements Action
{
    /**
     * Executes the action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $changedPHPFiles = $repository->getIndexOperator()->getStagedFilesOfType('php');

        $io->write('linting files:', true, IO::VERBOSE);
        foreach ($changedPHPFiles as $file) {
            $io->write('  - ' . $file, true, IO::VERBOSE);
            if ($this->hasSyntaxErrors($file)) {
                $io->write('    syntax error detected in ' . $file, true, IO::VERBOSE);
                throw new ActionFailed('linting failed: PHP syntax errors in ' . $file);
            }
        }
        $io->write('<info>No syntax errors detected</info>');
    }

    /**
     * Lint a php file
     *
     * @param  string $file
     * @return bool
     */
    protected function hasSyntaxErrors($file): bool
    {
        $process = new Processor();
        $result  = $process->run('php -l ' . escapeshellarg($file));

        return !$result->isSuccessful();
    }
}
