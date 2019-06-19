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

use CaptainHook\App\CH;
use CaptainHook\App\Config;
use CaptainHook\App\Hook\Util as HookUtil;
use CaptainHook\App\Storage\File\Json;

/**
 * Class Factory
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Factory
{
    /**
     * Config factory method
     *
     * @param  string $path
     * @return \CaptainHook\App\Config
     */
    public static function create(string $path = '') : Config
    {
        $factory = new static();

        return $factory->createConfig($path);
    }

    /**
     * Create a CaptainHook configuration
     *
     * @param  string $path
     * @return \CaptainHook\App\Config
     * @throws \Exception
     */
    public function createConfig($path = '') : Config
    {
        $path       = $path ?: getcwd() . DIRECTORY_SEPARATOR . CH::CONFIG;
        $json       = new Json($path);
        $fileExists = $json->exists();
        $config     = new Config($path, $fileExists);

        if ($fileExists) {
            $this->configure($config, $json->readAssoc());
        }

        return $config;
    }

    /**
     * Initialize the configuration with data load from config file
     *
     * @param  \CaptainHook\App\Config $config
     * @param  array                   $json
     * @return void
     * @throws \Exception
     */
    protected function configure(Config $config, array $json) : void
    {
        Util::validateJsonConfiguration($json);

        foreach (HookUtil::getValidHooks() as $hook => $class) {
            if (isset($json[$hook])) {
                $this->configureHook($config->getHookConfig($hook), $json[$hook]);
            }
        }

        $this->appendIncludedConfigurations($config, $json);
    }

    /**
     * Setup a hook configuration by json data
     *
     * @param  \CaptainHook\App\Config\Hook $config
     * @param  array                        $json
     * @return void
     * @throws \Exception
     */
    protected function configureHook(Config\Hook $config, array $json) : void
    {
        $config->setEnabled($json['enabled']);
        foreach ($json['actions'] as $actionJson) {
            $options    = isset($actionJson['options']) && is_array($actionJson['options'])
                        ? $actionJson['options']
                        : [];
            $conditions = isset($actionJson['conditions']) && is_array($actionJson['conditions'])
                        ? $actionJson['conditions']
                        : [];
            $config->addAction(new Config\Action($actionJson['action'], $options, $conditions));
        }
    }

    /**
     * Append all included configuration to the current configuration
     *
     * @param \CaptainHook\App\Config $config
     * @param array                   $json
     */
    private function appendIncludedConfigurations(Config $config, array $json)
    {
        $includes = $this->loadIncludedConfigs($json, $config->getPath());
        foreach (HookUtil::getValidHooks() as $hook => $class) {
            foreach ($includes as $includedConfig) {
                $this->copyActionsFromTo($includedConfig->getHookConfig($hook), $config->getHookConfig($hook));
            }
        }
    }

    /**
     * Read included configurations to add them to the main configuration later
     *
     * @param  array  $json
     * @param  string $path
     * @return \CaptainHook\App\Config[]
     */
    protected function loadIncludedConfigs(array $json, string $path) : array
    {
        $includes  = [];
        $directory = dirname($path);
        $files     = isset($json['config']['includes']) && is_array($json['config']['includes'])
                   ? $json['config']['includes']
                   : [];

        foreach ($files as $file) {
            $includes[] = self::create($directory . DIRECTORY_SEPARATOR . $file);
        }
        return $includes;
    }

    /**
     * Append actions for a given hook from all included configurations to the current configuration
     *
     * @param \CaptainHook\App\Config\Hook $includedConfig
     * @param \CaptainHook\App\Config\Hook $hookConfig
     */
    private function copyActionsFromTo(Hook $includedConfig, Hook $hookConfig)
    {
        foreach ($includedConfig->getActions() as $action) {
            $hookConfig->addAction($action);
        }
    }
}
