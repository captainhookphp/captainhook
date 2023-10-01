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

use CaptainHook\App\Config;
use CaptainHook\App\Hook\Template;

abstract class Local implements Template
{
    /**
     * All template related path information
     *
     * @var \CaptainHook\App\Hook\Template\PathInfo
     */
    protected PathInfo $pathInfo;

    /**
     * CaptainHook configuration
     *
     * @var \CaptainHook\App\Config
     */
    protected Config $config;

    /**
     * Is the executable a phar file
     *
     * @var bool
     */
    protected bool $isPhar;

    /**
     * Local constructor
     *
     * @param \CaptainHook\App\Hook\Template\PathInfo $pathInfo
     * @param \CaptainHook\App\Config                 $config
     * @param bool                                    $isPhar
     */
    public function __construct(PathInfo $pathInfo, Config $config, bool $isPhar)
    {
        $this->pathInfo = $pathInfo;
        $this->config   = $config;
        $this->isPhar   = $isPhar;
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
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return array<string>
     */
    abstract protected function getHookLines(string $hook): array;
}
