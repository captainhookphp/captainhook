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
use CaptainHook\App\Config\Options;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use Exception;
use SebastianFeldmann\Git\Repository;

/**
 * Class NotContainsRegex
 *
 * @package CaptainHook
 * @author  Felix Edelmann <fxedel@gmail.com>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   TODO
 */
class DoesNotContainRegex implements Action
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
        $regex = $options->get('regex');
        if ($regex === null) {
            throw new Exception('Missing option "regex" for DoesNotContainRegex action');
        }

        $files = $this->getFiles($options, $repository);

        $failedFiles = 0;
        $totalMatchesCount = 0;
        foreach ($files as $file) {
            $fileContent = file_get_contents($file);
            $matchCount = preg_match_all($regex, $fileContent, $matches);

            if ($matchCount > 0) {
                $io->write('- <error>FAIL</error> ' . $file . ' - ' . $matchCount . ' matches', true);
                $failedFiles++;
                $totalMatchesCount += $matchCount;
            } else {
                $io->write('- <info>OK</info> ' . $file, true, IO::VERBOSE);
            }
        }

        if ($failedFiles > 0) {
            $regexName = $options->get('regexName', $regex);
            throw new ActionFailed('<error>Regex \'' . $regexName . '\' failed:</error> ' . $totalMatchesCount . ' matches in ' . $failedFiles . ' files');
        }

        $io->write('<info>No regex matches found</info>');
    }

    /**
     * Returns the files that need to be checked.
     * 
     * @param  \CaptainHook\App\Config\Options   $options
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @return array
     */
    protected function getFiles(Options $options, Repository $repository): array
    {
        $index = $repository->getIndexOperator();

        $fileExtensions = $options->get('fileExtensions');
        if ($fileExtensions !== null) {
            $files = [];
            foreach ($fileExtensions as $ext) {
                $files = array_merge($files, $index->getStagedFilesOfType($ext));
            }
            return $files;
        }

        return $index->getStagedFiles();
    }
}
