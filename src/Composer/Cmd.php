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
        $extra  = self::getExtraConfig($event);
        $config = self::extract(CH::COMPOSER_CONFIG, $extra);
        $repo   = self::extract(CH::COMPOSER_GIT_DIR, $extra);
        $app    = self::createApplication($event, $config);

        self::configure($app, $config);
        self::install($app, $config, $repo);
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
     * @param  string                                $repo
     * @return void
     * @throws \Exception
     */
    private static function install(Application $app, string $config, string $repo) : void
    {
        $options = ['command' => 'install', '-f' => '-f'];
        self::appendNotEmpty($config, '--configuration', $options);
        self::appendNotEmpty($repo, '--git-directory', $options);

        $install = new Install();
        $install->setIO($app->getIO());
        $input   = new ArrayInput($options);
        $app->add($install);
        $app->run($input);
    }

    /**
     * Return Composer extra config, make sure it is an array
     *
     * @param \Composer\Script\Event $event
     * @return array
     */
    private static function getExtraConfig(Event $event) : array
    {
        $extra = $event->getComposer()->getPackage()->getExtra();
        return is_array($extra) ? $extra : [];
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

    /**
     * Adds a value to an array with a given key if the value is not empty
     *
     * @param  string $value
     * @param  string $key
     * @param  array  $array
     * @return void
     */
    private static function appendNotEmpty(string $value, string $key, array &$array) : void
    {
        if (!empty($value)) {
            $array[$key] = $value;
        }
    }
}
