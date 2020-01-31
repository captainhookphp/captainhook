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

namespace CaptainHook\App\Hook\Template\Local;

use CaptainHook\App\CH;
use CaptainHook\App\Hook\Template;
use SebastianFeldmann\Camino\Path;
use SebastianFeldmann\Camino\Path\Directory;

/**
 * Local class
 *
 * Generates the sourcecode for the php hook scripts in .git/hooks/*.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.0.0
 */
class Shell extends Template\Local
{
    /**
     * Return the path to the target path from the git repository root f.e. vendor/bin/captainhook
     *
     * @param  \SebastianFeldmann\Camino\Path\Directory $repo
     * @param  \SebastianFeldmann\Camino\Path           $target
     * @return string
     */
    protected function getPathForHookTo(Directory $repo, Path $target): string
    {
        if (!$target->isChildOf($repo)) {
            return $target->getPath();
        }
        return $target->getRelativePathFrom($repo);
    }

    /**
     * Returns lines of code for the local src installation
     *
     * @param  string $hook
     * @return array<string>
     */
    protected function getHookLines(string $hook): array
    {
        return [
            '#!/bin/sh',
            '',
            '# installed by CaptainHook ' . CH::VERSION,
            '',
            $this->executablePath
                . ' --configuration=' . $this->configPath
                . ' --bootstrap=' . $this->bootstrap
                . ' hook:' . $hook . ' "$@"',
        ];
    }
}
