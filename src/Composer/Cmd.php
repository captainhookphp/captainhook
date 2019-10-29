<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Composer;

use CaptainHook\App\CH;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use CaptainHook\App\Console\Command\Configuration;
use CaptainHook\App\Console\Command\Install;
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
    public static function setup(Event $event) : void
    {
        $extra  = self::getExtraConfig($event);
        $config = self::extract(CH::COMPOSER_CONFIG, $extra, CH::CONFIG);
        $app    = self::createApplication($event->getIO(), $config);

        self::configure($app, $config);
        self::install($app, $config);
    }

    /**
     * @param  \CaptainHook\App\Composer\Application $app
     * @param  string                                $config
     * @return void
     * @throws \Exception
     */
    private static function configure(Application $app, string $config)
    {
        if (file_exists($config)) {
            $app->getIO()->write(('  <info>Using CaptainHook config: ' . $config . '</info>'));
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
     * @param  \CaptainHook\App\Composer\Application $app
     * @param  string                                $config
     * @return void
     * @throws \Exception
     */
    private static function install(Application $app, string $config) : void
    {
        $options = [
            'command'         => 'install',
            '--configuration' => $config,
            '-f'              => true
        ];
        $app->run(new ArrayInput($options));
    }

    /**
     * Return Composer extra config, make sure it is an array
     *
     * @param \Composer\Script\Event $event
     * @return array
     */
    private static function getExtraConfig(Event $event) : array
    {
        return $event->getComposer()->getPackage()->getExtra();
    }

    /**
     * Create a CaptainHook Composer application
     *
     * @param  \Composer\IO\IOInterface $io
     * @param  string                   $config
     * @return \CaptainHook\App\Composer\Application
     */
    private static function createApplication(IOInterface $io, string $config) : Application
    {
        $app = new Application();
        $app->setAutoExit(false);
        $app->setConfigFile($config);
        $app->setProxyIO($io);

        $install = new Install();
        $install->setIO($app->getIO());
        $app->add($install);

        $configuration = new Configuration();
        $configuration->setIO($app->getIO());
        $app->add($configuration);
        return $app;
    }

    /**
     * Extract a value from an array if not set it returns the given default
     *
     * @param  string $key
     * @param  array  $array
     * @param  string $default
     * @return string
     */
    private static function extract(string $key, array $array, string $default = '') : string
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
