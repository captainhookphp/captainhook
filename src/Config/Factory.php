<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Config;

use CaptainHook\App\Config;
use CaptainHook\App\Hook\Util;
use CaptainHook\App\Storage\File\Json;

/**
 * Class Factory
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Factory
{
    /**
     * Config factory method.
     *
     * @param  string $path
     * @return \CaptainHook\App\Config
     */
    public static function create($path = null)
    {
        $factory = new static();

        return $factory->createConfig($path);
    }

    /**
     * Create a CaptainHook configuration.
     *
     * @param  string $path
     * @return \CaptainHook\App\Config
     */
    public function createConfig($path = null)
    {
        $path       = $path ?: getcwd() . DIRECTORY_SEPARATOR . 'captainhook.json';
        $json       = new Json($path);
        $fileExists = $json->exists();
        $config     = new Config($path, $fileExists);

        if ($fileExists) {
            $this->configure($config, $json->readAssoc());
        }

        return $config;
    }

    /**
     * Initialize the configuration with data load from config file.
     *
     * @param \CaptainHook\App\Config $config
     * @param array            $json
     */
    protected function configure(Config $config, array $json)
    {
        foreach ($json as $hook => $data) {
            $this->configureHook($config->getHookConfig($hook), $data);
        }
    }

    /**
     * Setup a hook configuration by json data.
     *
     * @param \CaptainHook\App\Config\Hook $config
     * @param array                 $json
     */
    protected function configureHook(Config\Hook $config, array $json)
    {
        $config->setEnabled($json['enabled']);
        foreach ($json['actions'] as $actionJson) {
            $type = Util::getActionType($actionJson['action']);
            $config->addAction(new Config\Action($type, $actionJson['action'], $actionJson['options']));
        }
    }
}
