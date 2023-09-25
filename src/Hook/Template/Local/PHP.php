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
 * @since   Class available since Release 4.3.0
 */
class PHP extends Template\Local
{
    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return array<string>
     */
    public function getHookLines(string $hook): array
    {
        return $this->isPhar ? $this->getPharHookLines($hook) : $this->getSrcHookLines($hook);
    }

    /**
     * Returns lines of code for the local src installation
     *
     * @param  string $hook
     * @return array<string>
     */
    private function getSrcHookLines(string $hook): array
    {
        $configPath = $this->pathInfo->getConfigPath();
        $bootstrap  = $this->config->getBootstrap();

        return [
            '#!/usr/bin/env php',
            '<?php',
            '',
            '# installed by CaptainHook ' . CH::VERSION,
            '',
            'use CaptainHook\App\Console\Application as CaptainHook;',
            'use Symfony\Component\Console\Input\ArgvInput;',
            '',
            '(static function($argv)',
            '{',
            '    $bootstrap = ' . dirname($configPath) . '/' . $bootstrap . '\';',
            '    if (!file_exists($bootstrap)) {',
            '        fwrite(STDERR, \'Boostrap file \\\'\' . $bootstrap . \'\\\' could not be found\');',
            '        exit(1);',
            '    }',
            '    require $bootstrap;',
            '',
            '    $argv = array_merge(',
            '        [',
            '            $argv[0],',
            '            \'hook:' . $hook . '\',',
            '            \'--configuration=' . $configPath . '\'',
            '            \'--git-directory=\' . dirname(__DIR__, 2) . \'/.git\',',
            '        ],',
            '        array_slice($argv, 1)',
            '    );',
            '    $captainHook = new CaptainHook($argv[0]);',
            '    $captainHook->run(new ArgvInput($argv));',
            '}',
            ')($argv);',
        ];
    }

    /**
     * Returns the lines of code for the local phar installation
     *
     * @param  string $hook
     * @return array<string>
     */
    private function getPharHookLines(string $hook): array
    {
        $configPath     = $this->pathInfo->getConfigPath();
        $executablePath = $this->pathInfo->getExecutablePath();
        $bootstrap      = $this->config->getBootstrap();

        $executableInclude = substr($executablePath, 0, 1) == '/'
                           ? '\'' . $executablePath . '\''
                           : '__DIR__ . \'/../../' . $executablePath  . '\'';
        return [
            '#!/usr/bin/env php',
            '<?php',
            '',
            '(static function($argv)',
            '{',
            '    $argv = array_merge(',
            '        [',
            '            $argv[0],',
            '            \'hook:' . $hook . '\',',
            '            \'--configuration=' . $configPath . ',',
            '            \'--git-directory=\' . dirname(__DIR__, 2) . \'/.git\',',
            '            \'--bootstrap=' . $bootstrap . '\',',
            '        ],',
            '        array_slice($argv, 1)',
            '    );',
            '    include ' . $executableInclude . ';',
            '}',
            ')($argv);',
        ];
    }
}
