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
 * Class Check
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.4.1
 */
abstract class Check implements Action
{
    /**
     * Actual action name
     *
     * @var string
     */
    protected $actionName;

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
     * Extract files list from the action configuration
     *
     * @param  \CaptainHook\App\Config\Options $options
     * @return array
     * @throws \Exception
     */
    protected function getFiles(Config\Options $options): array
    {
        $files = $options->get('files');
        if (!is_array($files)) {
            throw new Exception('Missing option "files" for ' . $this->actionName . ' action');
        }

        $globs = [];
        foreach ($files as $glob) {
            $globs[$glob] = glob($glob);
        }
        return $globs;
    }

    /**
     * Check if all files are empty
     *
     * @param  string[] $files
     * @return bool
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function areAllFilesEmpty(array $files): bool
    {
        foreach ($files as $file) {
            if (!$this->isEmpty($file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if any file is empty
     *
     * @param  array $files
     * @return bool
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function isAnyFileEmpty(array $files): bool
    {
        foreach ($files as $file) {
            if ($this->isEmpty($file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true when the file has no contents or the directory is empty
     *
     * @param  string $file
     * @return bool
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function isEmpty(string $file): bool
    {
        if (is_dir($file)) {
            return $this->isDirectoryEmpty($file);
        }

        return filesize($file) === 0;
    }

    /**
     * Checks if a directory is empty
     *
     * @param  string $directory
     * @return bool
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function isDirectoryEmpty(string $directory)
    {
        // ignore . and .. directories
        return empty(array_diff(scandir($directory), ['..', '.']));
    }
}
