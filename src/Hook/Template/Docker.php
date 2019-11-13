<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Hook\Template;
use CaptainHook\App\Storage\Util;

/**
 * Docker class
 *
 * Generates the bash scripts placed in .git/hooks/* for every hook
 * to execute CaptainHook inside of a Docker container.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.3.0
 */
class Docker implements Template
{
    private const BINARY = 'captainhook-run';

    /**
     * Path to the captainhook-run binary
     *
     * @var string
     */
    private $binaryPath;

    /**
     * Command to spin up the container
     *
     * @var string
     */
    private $command;

    /**
     * Docker constructor
     *
     * @param string $repoPath
     * @param string $vendorPath
     * @param string $command
     */
    public function __construct(string $repoPath, string $vendorPath, string $command)
    {
        $this->command    = $command;
        $this->binaryPath = $this->resolveBinaryPath($repoPath, $vendorPath);
    }

    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return string
     */
    public function getCode(string $hook): string
    {
        return '#!/usr/bin/env bash' . PHP_EOL .
            $this->command . ' ' . $this->binaryPath . ' ' . $hook . ' "$@"' . PHP_EOL;
    }

    /**
     * Resolves the path to the captainhook-run binary and returns it.
     *
     * This path is either right inside the repo itself (captainhook) or only in vendor path.
     * Which happens if captainhook is required as dependency.
     *
     * @param  string $repoPath   Absolute path to the git repository root
     * @param  string $vendorPath Absolute path to the composer vendor directory
     * @return string
     */
    private function resolveBinaryPath(string $repoPath, string $vendorPath): string
    {
        // For docker we need to strip down the current working directory.
        // This is caused because docker will always connect to a specific working directory
        // where the absolute path will not be recognized.
        // E.g.:
        //   cwd    => /project/
        //   path   => /project/vendor/bin/captainhook-run
        //   docker => ./vendor/bin/captainhook-run

        // check if the captainhook binary is in the repository root directory
        // this is only the case if we work in the captainhook repository
        if (file_exists($repoPath . '/' . self::BINARY)) {
            return './' . self::BINARY;
        }

        $repoDir   = Util::pathToArray($repoPath);
        $vendorDir = Util::pathToArray($vendorPath);

        // if vendor dir is a subdirectory use a relative path
        if (Util::isSubDirectoryOf($vendorDir, $repoDir)) {
            $vendorPath = './' . Util::arrayToPath(Util::getSubPathOf($vendorDir, $repoDir));
        }

        // by default this should return something like ./vendor/bin/captainhook-run
        // if the vendor directory is not located in your git repository it will return an absolute path
        return $vendorPath . '/bin/' . self::BINARY;
    }
}
