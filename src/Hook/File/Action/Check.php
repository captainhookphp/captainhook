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
use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Restriction;
use Exception;
use SebastianFeldmann\Git\Repository;

/**
 * Class Check
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.4.1
 */
abstract class Check implements Action, Constrained
{
    /**
     * Actual action name
     *
     * @var string
     */
    protected $actionName;

    /**
     * Make sure this action is only used pro pre-commit hooks
     *
     * @return \CaptainHook\App\Hook\Restriction
     */
    public static function getRestriction(): Restriction
    {
        return new Restriction('pre-commit');
    }

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
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void;

    /**
     * @param  \CaptainHook\App\Config\Options $options
     * @param  string[]                        $stagedFiles
     * @return array
     * @throws \Exception
     */
    protected function getFilesToCheck(Config\Options $options, array $stagedFiles): array
    {
        return $this->extractFilesToCheck($this->getFilesToWatch($options), $stagedFiles);
    }

    /**
     * Return a list of files to watch
     *
     * ['pattern1' => ['file1', 'file2'], 'pattern2' => ['file3']...]
     *
     * @param  \CaptainHook\App\Config\Options $options
     * @return array
     * @throws \Exception
     */
    private function getFilesToWatch(Config\Options $options): array
    {
        $filesToWatch = [];
        $filePatterns = $options->get('files');
        if (!is_array($filePatterns)) {
            throw new Exception('Missing option "files" for ' . $this->actionName . ' action');
        }

        // collect all files that should be watched
        foreach ($filePatterns as $glob) {
            $filesToWatch[$glob] = glob($glob);
        }

        return $filesToWatch;
    }

    /**
     * Extract files list from the action configuration
     *
     * @param  array    $filesToWatch  ['pattern1' => ['file1', 'file2'], 'pattern2' => ['file3']...]
     * @param  string[] $stagedFiles
     * @return array
     */
    private function extractFilesToCheck(array $filesToWatch, array $stagedFiles): array
    {
        $filesToCheck = [];
        // check if any staged file should be watched
        foreach ($stagedFiles as $stagedFile) {
            if ($this->isFileUnderWatch($stagedFile, $filesToWatch)) {
                $filesToCheck[] = $stagedFile;
            }
        }
        return $filesToCheck;
    }

    /**
     * Check if a file is in the list of watched files
     *
     * @param  string $stagedFile
     * @param  array  $filesToWatch
     * @return bool
     */
    protected function isFileUnderWatch(string $stagedFile, array $filesToWatch): bool
    {
        // check the list of files found for each pattern
        foreach ($filesToWatch as $pattern => $files) {
            foreach ($files as $fileToWatch) {
                if ($fileToWatch === $stagedFile) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns true if the file has no contents
     *
     * @param  string $file
     * @return bool
     */
    protected function isEmpty(string $file): bool
    {
        return filesize($file) === 0;
    }
}
