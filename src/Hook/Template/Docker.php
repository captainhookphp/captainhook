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
        $this->binaryPath = ltrim(
            Util::resolveBinaryPath($repoPath, $vendorPath, 'captainhook-run'),
            DIRECTORY_SEPARATOR
        );
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
            $this->command . ' ./' . $this->binaryPath . ' ' . $hook . ' "$@"' . PHP_EOL;
    }
}
