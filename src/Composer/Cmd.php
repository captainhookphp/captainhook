<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Composer;

use CaptainHook\App\CH;
use CaptainHook\App\Console\Application\Composer as ComposerApplication;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class Cmd
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Cmd
{
    /**
     * Gets called by composer after a successful package installation
     *
     * @param  \Composer\Script\Event $event
     * @return void
     * @throws \Exception
     */
    public static function setup(Event $event): void
    {
        $io     = $event->getIO();
        $extra  = self::getExtraConfig($event);
        $config = self::extract(CH::COMPOSER_CONFIG, $extra, CH::CONFIG);
        $app    = ComposerApplication::create($io);

        self::configure($io, $app, $config);
        self::install($app, $config);
    }

    /**
     * Execute guided setup
     *
     * @param  \Composer\IO\IOInterface                      $io
     * @param  \CaptainHook\App\Console\Application\Composer $app
     * @param  string                                        $config
     * @return void
     * @throws \Exception
     */
    private static function configure(IOInterface $io, ComposerApplication $app, string $config)
    {
        if (file_exists($config)) {
            $io->write(('  <info>Using CaptainHook config: ' . $config . '</info>'));
            return;
        }
        $options = [
            'command'         => 'configure',
            '--configuration' => $config
        ];
        $app->run(new ArrayInput($options));
    }

    /**
     * Installs the hooks to your local repository
     *
     * @param  \CaptainHook\App\Console\Application\Composer $app
     * @param  string                                        $config
     * @return void
     * @throws \Exception
     */
    private static function install(ComposerApplication $app, string $config): void
    {
        $options = [
            'command'         => 'install',
            '--configuration' => $config,
            '--git-directory' => self::findGitDir($config),
            '-f'              => true
        ];
        $app->run(new ArrayInput($options));
    }

    /**
     * Return Composer extra config, make sure it is an array
     *
     * @param \Composer\Script\Event $event
     * @return array<string, string>
     */
    private static function getExtraConfig(Event $event): array
    {
        return $event->getComposer()->getPackage()->getExtra();
    }

    /**
     * Extract a value from an array if not set it returns the given default
     *
     * @param  string                 $key
     * @param  array<string, string>  $array
     * @param  string                 $default
     * @return string
     */
    private static function extract(string $key, array $array, string $default = ''): string
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Search for the git repository to store the hooks in
     *
     * @param  string $config
     * @return string
     */
    private static function findGitDir(string $config): string
    {
        $path = \dirname($config);

        while (file_exists($path)) {
            $possibleGitDir = $path . '/.git';
            if (file_exists($possibleGitDir)) {
                return $possibleGitDir;
            }
            $path = \dirname($path);
        }
        throw new RuntimeException('git directory not found');
    }
}
