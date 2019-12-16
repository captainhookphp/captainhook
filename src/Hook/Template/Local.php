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

use CaptainHook\App\Hook\Template;
use SebastianFeldmann\Camino\Path;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

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
class Local implements Template
{
    /**
     * Path to the captainhook configuration
     *
     * @var string
     */
    private $configPath;

    /**
     * Original bootstrap option
     *
     * @var string
     */
    private $bootstrap;

    /**
     * Path to the vendor directory
     *
     * @var string
     */
    private $bootstrapPath;

    /**
     * Path to the captainhook executable
     *
     * @var string
     */
    private $executablePath;

    /**
     * Is the executable a phar file
     *
     * @var bool
     */
    private $isPhar;

    /**
     * Local constructor
     *
     * @param \SebastianFeldmann\Camino\Path\Directory $repo
     * @param \SebastianFeldmann\Camino\Path\File      $config
     * @param \SebastianFeldmann\Camino\Path\File      $captainHook
     * @param string                                   $bootstrap
     * @param bool                                     $isPhar
     */
    public function __construct(Directory $repo, File $config, File $captainHook, string $bootstrap, bool $isPhar)
    {
        $bootstrapDir         = new Directory($config->getDirectory()->getPath() . '/' . $bootstrap);
        $this->bootstrap      = $bootstrap;
        $this->bootstrapPath  = $this->getPathFromHookTo($repo, $bootstrapDir);
        $this->configPath     = $this->getPathFromHookTo($repo, $config);
        $this->executablePath = $this->getPathFromHookTo($repo, $captainHook);
        $this->isPhar         = $isPhar;
    }

    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return string
     */
    public function getCode(string $hook): string
    {
        $lines = $this->isPhar ? $this->getPharHookLines($hook) : $this->getLocalHookLines($hook);

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    /**
     * Returns lines of code for the local src installation
     *
     * @param  string $hook
     * @return array<string>
     */
    private function getLocalHookLines(string $hook): array
    {
        return [
            '#!/usr/bin/env php',
            '<?php',
            '',
            'use CaptainHook\App\Console\Application\Cli as CaptainHook;',
            'use Symfony\Component\Console\Input\ArgvInput;',
            '',
            '(static function($argv)',
            '{',
            '    $bootstrap = ' . $this->bootstrapPath . ';',
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
            '            \'--configuration=\' . ' . $this->configPath . ',',
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
            '            \'--configuration=\' . ' . $this->configPath . ',',
            '            \'--git-directory=\' . dirname(__DIR__, 2) . \'/.git\',',
            '            \'--bootstrap=' . $this->bootstrap . '\',',
            '        ],',
            '        array_slice($argv, 1)',
            '    );',
            '    include ' . $this->executablePath . ';',
            '}',
            ')($argv);',
        ];
    }

    /**
     * Return the path to the target path from inside the .git/hooks directory f.e. __DIR__ ../../vendor
     *
     * @param  \SebastianFeldmann\Camino\Path\Directory $repo
     * @param  \SebastianFeldmann\Camino\Path           $target
     * @return string
     */
    private function getPathFromHookTo(Directory $repo, Path $target): string
    {
        if (!$target->isChildOf($repo)) {
            return '\'' . $target->getPath() . '\'';
        }

        return '__DIR__ . \'/../../' . $target->getRelativePathFrom($repo) . '\'';
    }
}
