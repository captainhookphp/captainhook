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
    public static function configure(Event $event) : void
    {
        $config = self::getCaptainHookConfig($event);

        if (file_exists($config)) {
            $event->getIO()->write(('  <info>Skipping configuration: config file exists</info>'));
            return;
        }

        $app           = self::createApplication($event, $config);
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
     * @param  \Composer\Script\Event $event
     * @return void
     * @throws \Exception
     */
    public static function install(Event $event) : void
    {
        $config  = self::getCaptainHookConfig($event);
        $app     = self::createApplication($event, $config);
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
        $config = $event->getComposer()->getConfig();
        $extra  = $config->get('extra');
        if ($extra === null || ! isset($extra['captainhookconfig'])) {
            return getcwd() . DIRECTORY_SEPARATOR . CH::CONFIG;
        }
        return $extra['captainhookconfig'];
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
