<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\CH;
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hook\Template\Docker\Config as DockerConfig;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

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
    private const BINARY = 'captainhook';

    /**
     * Original bootstrap option, relative path from the config file
     *
     * @var string
     */
    private $bootstrap;

    /**
     * Docker configuration with run-command & run-path
     *
     * @var \CaptainHook\App\Hook\Template\Docker\Config
     */
    private $dockerConfig;

    /**
     * Path to the CaptainHook binary script or PHAR
     *
     * @var string
     */
    private $binaryPath;

    /**
     * Docker constructor
     *
     * @param \SebastianFeldmann\Camino\Path\Directory     $repo
     * @param \SebastianFeldmann\Camino\Path\File          $config
     * @param \SebastianFeldmann\Camino\Path\File          $captain
     * @param \CaptainHook\App\Hook\Template\Docker\Config $docker
     * @param string                                       $bootstrap
     */
    public function __construct(Directory $repo, File $config, File $captain, DockerConfig $docker, string $bootstrap)
    {
        $this->bootstrap    = $bootstrap;
        $this->dockerConfig = $docker;
        $this->binaryPath   = $this->resolveBinaryPath($repo, $captain);
    }

    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return string
     */
    public function getCode(string $hook): string
    {
        $lines = [
            '#!/bin/sh',
            '',
            '# installed by CaptainHook ' . CH::VERSION,
            '',
            $this->dockerConfig->getDockerCommand() . ' ' . $this->binaryPath . ' hook:' . $hook . ' "$@"'
        ];

         return implode(PHP_EOL, $lines);
    }

    /**
     * Resolves the path to the captainhook-run binary and returns it.
     *
     * This path is either right inside the repo itself (captainhook) or only in vendor path.
     * Which happens if captainhook is required as dependency.
     *
     * @param  \SebastianFeldmann\Camino\Path\Directory $repo       Absolute path to the git repository root
     * @param  \SebastianFeldmann\Camino\Path\File      $executable Absolute path to the executable
     * @return string
     */
    private function resolveBinaryPath(Directory $repo, File $executable): string
    {
        // if a specific executable is configured use just that
        if (!empty($this->dockerConfig->getPathToCaptainHookExecutable())) {
            return $this->dockerConfig->getPathToCaptainHookExecutable();
        }

        // check if the captainhook binary is in the repository bin directory
        // this is only the case if we work in the captainhook repository
        if (file_exists($repo->getPath() . '/bin/' . self::BINARY)) {
            return './bin/' . self::BINARY;
        }

        // For docker we need to strip down the current working directory.
        // This is caused because docker will always connect to a specific working directory
        // where the absolute path will not be recognized.
        // E.g.:
        //   cwd    => /project/
        //   path   => /project/vendor/bin/captainhook-run
        //   docker => ./vendor/bin/captainhook-run
        $pathToExecutable = $executable->getPath();

        // if executable is located inside the repository use a relative path
        // by default this should return something like ./vendor/bin/captainhook
        if ($executable->isChildOf($repo)) {
            $pathToExecutable = './' . $executable->getRelativePathFrom($repo);
        }

        // if the executable is not located in your git repository it will return the absolute path
        return $pathToExecutable;
    }
}
