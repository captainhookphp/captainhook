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

namespace CaptainHook\App\Hook\Template\Docker;

/**
 * Class Config
 *
 * @package CaptainHook\App
 */
class Config
{
    /**
     * Docker command to execute
     *
     * @var string
     */
    private $command;

    /**
     * Custom Path to captainhook binary to execute
     * @var string
     */
    private $path;

    /**
     * Config constructor.
     *
     * @param string $command
     * @param string $path
     */
    public function __construct(string $command, string $path)
    {
        $this->command = $command;
        $this->path    = $path;
    }

    /**
     * @return string
     */
    public function getDockerCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getPathToCaptainHookExecutable(): string
    {
        return $this->path;
    }
}
