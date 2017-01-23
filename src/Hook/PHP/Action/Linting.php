<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\PHP\Action;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO;
use SebastianFeldmann\CaptainHook\Exception\ActionFailed;
use SebastianFeldmann\CaptainHook\Hook\Action;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

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
     * @param  \SebastianFeldmann\CaptainHook\Config         $config
     * @param  \SebastianFeldmann\CaptainHook\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \SebastianFeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $changedPHPFiles = $repository->getIndexOperator()->getStagedFilesOfType('php');

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
    protected function hasSyntaxErrors($file) : bool
    {
        $process = new Processor();
        $result  = $process->run('php -l ' . escapeshellarg($file));

        return !$result->isSuccessful();
    }
}
