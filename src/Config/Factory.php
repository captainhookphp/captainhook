<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Config;

use HookMeUp\App\Config;
use HookMeUp\App\Hook\Util;
use HookMeUp\App\Storage\File\Json;

/**
 * Class Factory
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Factory
{
    /**
     * Config factory method.
     *
     * @param  string $path
     * @return \HookMeUp\App\Config
     */
    public static function create($path = null)
    {
        $factory = new static();

        return $factory->createConfig($path);
    }

    /**
     * Create a HookMeUp configuration.
     *
     * @param  string $path
     * @return \HookMeUp\App\Config
     */
    public function createConfig($path = null)
    {
        $path       = $path ?: getcwd() . DIRECTORY_SEPARATOR . 'hookmeup.json';
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
     * @param \HookMeUp\App\Config $config
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
     * @param \HookMeUp\App\Config\Hook $config
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
