<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\PHP\Action;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Exception\ActionFailed;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Action;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

/**
 * Class Linter
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.0.5
 */
class Linting implements Action
{
    /**
     * Executes the action.
     *
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $changedPHPFiles = $repository->getChangedFilesResolver()->getChangedFilesOfType('php');

        $io->write('linting files:', true, IO::VERBOSE);
        foreach ($changedPHPFiles as $file) {
            $io->write('  - ' . $file, true, IO::VERBOSE);
            if ($this->hasSyntaxErrors($file)) {
                throw ActionFailed::withMessage('syntax errors in file: ' . $file);
            }
        }
        $io->write('<info>no syntax errors detected</info>');
    }

    /**
     * Lint a php file.
     *
     * @param  string $file
     * @return bool
     */
    protected function hasSyntaxErrors($file)
    {
        $process = new Process('php -l ' . ProcessUtils::escapeArgument($file));
        $process->setTimeout(null);
        $process->run();

        return !$process->isSuccessful();
    }
}
