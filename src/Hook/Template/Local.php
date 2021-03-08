<?php

/**
 * This file is part of captainhook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Hook\Template;
use SebastianFeldmann\Camino\Path;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

abstract class Local implements Template
{
    /**
     * Path to the captainhook configuration
     *
     * @var string
     */
    protected $configPath;

    /**
     * Original bootstrap option
     *
     * @var string
     */
    protected $bootstrap;

    /**
     * Path to the captainhook executable
     *
     * @var string
     */
    protected $executablePath;

    /**
     * Is the executable a phar file
     *
     * @var bool
     */
    protected $isPhar;

    /**
     * Path to the php binary
     *
     * @var string
     */
    protected $phpPath;

    /**
     * Local constructor
     *
     * @param \SebastianFeldmann\Camino\Path\Directory $repo
     * @param \SebastianFeldmann\Camino\Path\File      $config
     * @param \SebastianFeldmann\Camino\Path\File      $captainHook
     * @param string                                   $bootstrap
     * @param bool                                     $isPhar
     * @param string                                   $phpPath
     */
    public function __construct(
        Directory $repo,
        File $config,
        File $captainHook,
        string $bootstrap,
        bool $isPhar,
        string $phpPath
    ) {
        $this->bootstrap      = $bootstrap;
        $this->configPath     = $this->getPathForHookTo($repo, $config);
        $this->executablePath = $this->getPathForHookTo($repo, $captainHook);
        $this->isPhar         = $isPhar;
        $this->phpPath        = $phpPath;
    }

    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return string
     */
    public function getCode(string $hook): string
    {
        return implode(PHP_EOL, $this->getHookLines($hook)) . PHP_EOL;
    }

    /**
     * Return the path to the target path from inside the .git/hooks directory f.e. __DIR__ ../../vendor
     *
     * @param  \SebastianFeldmann\Camino\Path\Directory $repo
     * @param  \SebastianFeldmann\Camino\Path           $target
     * @return string
     */
    abstract protected function getPathForHookTo(Directory $repo, Path $target): string;

    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return array<string>
     */
    abstract protected function getHookLines(string $hook): array;
}
