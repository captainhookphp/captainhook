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
use CaptainHook\App\Config;
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hooks;

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
     * All path required for template creation
     *
     * @var \CaptainHook\App\Hook\Template\PathInfo
     */
    private PathInfo $pathInfo;

    /**
     * CaptainHook configuration
     *
     * @var \CaptainHook\App\Config
     */
    private Config $config;

    /**
     * Path to the CaptainHook binary script or PHAR
     *
     * @var string
     */
    private string $binaryPath;

    /**
     * Docker constructor
     *
     * @param \CaptainHook\App\Hook\Template\PathInfo $pathInfo
     * @param \CaptainHook\App\Config                 $config
     */
    public function __construct(PathInfo $pathInfo, Config $config)
    {
        $this->pathInfo   = $pathInfo;
        $this->config     = $config;
        $this->binaryPath = $this->resolveBinaryPath();
    }

    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return string
     */
    public function getCode(string $hook): string
    {
        $path2Config = $this->pathInfo->getConfigPath();
        $config      = $path2Config !== CH::CONFIG ? ' --configuration=' . escapeshellarg($path2Config) : '';
        $bootstrap   = !empty($this->config->getBootstrap()) ? ' --bootstrap=' . $this->config->getBootstrap() : '';
        $tty         = Hooks::allowsUserInput($hook) ? 'exec < /dev/tty' : '';

        $lines = [
            '#!/bin/sh',
            $tty,
            '# installed by CaptainHook ' . CH::VERSION,
            '',
            $this->getOptimizeDockerCommand($hook) . ' '
            . $this->binaryPath . ' hook:' . $hook
            . $config
            . $bootstrap
            . ' "$@"'
        ];
        return implode(PHP_EOL, $lines) . PHP_EOL;
    }


    /**
     * Returns the optimized docker exec command
     *
     * This tries to optimize the `docker exec` commands. Docker exec should always run in --interactive mode.
     * During hooks that could need user input it should use --tty.
     * In case of `commit -a` we have to pass the GIT_INDEX_FILE env variable so `git` inside the container
     * can recognize the temp index.
     *
     * @param  string $hook
     * @return string
     */
    private function getOptimizeDockerCommand(string $hook): string
    {
        $command  = $this->config->getRunExec();
        $position = strpos($command, 'docker exec');
        // add interactive and tty flags if docker exec is used
        if ($position !== false) {
            $endExec    = $position + 11;
            $executable = substr($command, 0, $endExec);
            $options    = substr($command, $endExec);
            $tty        = Hooks::allowsUserInput($hook) ? ' -t' : '';

            $interactive = !preg_match('# -[a-z]*i| --interactive#', $options) ? ' -i' : '';
            $useTTY      = !preg_match('# -[a-z]*t| --tty#', $options) ? $tty : '';
            $env         = !preg_match('# (-[a-z]*e|--env)[= ]+GIT_INDEX_FILE#', $options) ? ' -e GIT_INDEX_FILE' : '';
            $command     = trim($executable) . $interactive . $useTTY . $env . ' ' . trim($options);
        }
        return $command;
    }

    /**
     * Resolves the path to the captainhook binary and returns it
     *
     * @return string
     */
    private function resolveBinaryPath(): string
    {
        // if a specific executable is configured use just that
        if (!empty($this->config->getRunPath())) {
            return $this->config->getRunPath();
        }

        // For Docker we need to strip down the current working directory.
        // This is caused because docker will always connect to a specific working directory
        // where the absolute path will not be recognized.
        // E.g.:
        //   cwd    => /project/
        //   path   => /project/vendor/bin/captainhook
        //   docker => ./vendor/bin/captainhook
        // if the executable is located inside the repository we can use a relative path
        // by default this should return something like ./vendor/bin/captainhook
        // if the executable is not located in your git repository it will return the absolute path
        // which will most likely not work from within the docker container
        // you have to use the 'run_path' config then
        return $this->pathInfo->getExecutablePath();
    }
}
