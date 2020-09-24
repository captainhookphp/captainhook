<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use Exception;
use SebastianFeldmann\Git\Repository;

/**
 * Class IsEmpty
 *
 * @package CaptainHook
 * @author  Felix Edelmann <fxedel@gmail.com>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.4.0
 */
class IsEmpty implements Action
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
        $options = $action->getOptions();
        $files = $options->get('files');
        if ($files === null) {
            throw new Exception('Missing option "files" for IsEmpty action');
        }

        $failedFiles = 0;
        foreach ($files as $file) {
            if (!$this->isEmpty($file)) {
                $io->write('- <error>FAIL</error> ' . $file, true);
                $failedFiles++;
            } else {
                $io->write('- <info>OK</info> ' . $file, true, IO::VERBOSE);
            }
        }

        if ($failedFiles > 0) {
            throw new ActionFailed('<error>Error: ' . $failedFiles . ' non-empty file(s)</error>');
        }

        $io->write('<info>All files are empty or don\'t exist</info>');
    }

    /**
     * Returns true when the file is empty or doesn't exist
     *
     * @param  string $file
     * @return bool
     */
    protected function isEmpty($file): bool
    {
        if (!file_exists($file)) {
            return true;
        }

        return filesize($file) === 0;
    }
}
