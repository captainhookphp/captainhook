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
use CaptainHook\App\Console\IOUtil;
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
     * Path to php executable, default 'php'
     *
     * @var string
     */
    private $php;

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
        $this->php       = !empty($config->getPhpPath()) ? $config->getPhpPath() : 'php';
        $changedPHPFiles = $repository->getIndexOperator()->getStagedFilesOfType('php');
        $failedFiles     = 0;
        $messages        = [];

        foreach ($changedPHPFiles as $file) {
            $prefix = IOUtil::PREFIX_OK;
            if ($this->hasSyntaxErrors($file)) {
                $prefix = IOUtil::PREFIX_FAIL;
                $failedFiles++;
            }
            $messages[] = $prefix . ' ' . $file;
        }

        $io->write(['', '', implode(PHP_EOL, $messages), ''], true, IO::VERBOSE);

        if ($failedFiles > 0) {
            throw new ActionFailed(
                '<error>Linting failed:</error> PHP syntax errors in ' . $failedFiles . ' file(s)' . PHP_EOL
                . PHP_EOL
                . implode(PHP_EOL, $messages)
            );
        }
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
        $result  = $process->run($this->php . ' -l ' . escapeshellarg($file));

        return !$result->isSuccessful();
    }
}
