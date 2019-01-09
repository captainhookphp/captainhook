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
        $config = self::getCaptainHookConfig($event);
        $app    = self::createApplication($event, $config);

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
            $app->getIO()->write(('  <info>Skipping configuration: config file exists</info>'));
            return;
        }
        $configuration = new Configuration();
        $configuration->setIO($app->getIO());
        $input         = new ArrayInput(
            ['command' => 'configure', '--configuration' => $config, '-f' => '-f', '-e' => '-e']
        );
        $app->add($configuration);
        $app->run($input);
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
        $install = new Install();
        $install->setIO($app->getIO());
        $input   = new ArrayInput(['command' => 'install', '--configuration' => $config, '-f' => '-f']);
        $app->add($install);
        $app->run($input);
    }

    /**
     * Return the path to the captainhook config file
     *
     * @param  \Composer\Script\Event $event
     * @return string
     */
    private static function getCaptainHookConfig(Event $event) : string
    {
        $package = $event->getComposer()->getPackage();
        $extra   = $package->getExtra();
        if ($extra === null || ! isset($extra[CH::CONFIG_COMPOSER])) {
            return getcwd() . DIRECTORY_SEPARATOR . CH::CONFIG;
        }
        return $extra[CH::CONFIG_COMPOSER];
    }

    /**
     * Create a CaptainHook Composer application
     *
     * @param  \Composer\Script\Event $event
     * @param  string                 $config
     * @return \CaptainHook\App\Composer\Application
     */
    private static function createApplication(Event $event, string $config) : Application
    {
        $app = new Application();
        $app->setAutoExit(false);
        $app->setConfigFile($config);
        $app->setProxyIO($event->getIO());
        return $app;
    }
}
