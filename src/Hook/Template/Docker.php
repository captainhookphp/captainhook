<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
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
 * to execute CaptainHook inside a Docker container.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.3.0
 */
class Docker implements Template
{
    /**
     * Path to the repository root
     *
     * @var \SebastianFeldmann\Camino\Path\Directory
     */
    private $repository;

    /**
     * Path to the configuration file
     *
     * @var \SebastianFeldmann\Camino\Path\File
     */
    private $config;

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
     */
    public function __construct(Directory $repo, File $config, File $captain, DockerConfig $docker)
    {
        $this->repository   = $repo;
        $this->config       = $config;
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
        $path2Config = $this->config->getRelativePathFrom($this->repository);
        $config      = $path2Config !== CH::CONFIG ? ' --configuration=' . escapeshellarg($path2Config) : '';

        $lines = [
            '#!/bin/sh',
            '',
            '# installed by CaptainHook ' . CH::VERSION,
            '',
            $this->dockerConfig->getDockerCommand() . ' ' . $this->binaryPath . ' hook:' . $hook . $config . ' "$@"'
        ];
        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    /**
     * Resolves the path to the captainhook binary and returns it
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
        if (file_exists($repo->getPath() . '/bin/captainhook')) {
            return './bin/captainhook';
        }

        // For docker we need to strip down the current working directory.
        // This is caused because docker will always connect to a specific working directory
        // where the absolute path will not be recognized.
        // E.g.:
        //   cwd    => /project/
        //   path   => /project/vendor/bin/captainhook
        //   docker => ./vendor/bin/captainhook
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
