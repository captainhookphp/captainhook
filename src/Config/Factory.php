<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Config;

use CaptainHook\App\CH;
use CaptainHook\App\Config;
use CaptainHook\App\Hook\Util as HookUtil;
use CaptainHook\App\Storage\File\Json;
use RuntimeException;

/**
 * Class Factory
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 * @internal
 */
final class Factory
{
    /**
     * Maximal level in including config files
     *
     * @var int
     */
    private $maxIncludeLevel = 1;

    /**
     * Current level of inclusion
     *
     * @var int
     */
    private $includeLevel = 0;

    /**
     * Create a CaptainHook configuration
     *
     * @param  string $path
     * @param  array  $settings
     * @return \CaptainHook\App\Config
     * @throws \Exception
     */
    public function createConfig(string $path = '', array $settings = []): Config
    {
        $path = $path ?: getcwd() . DIRECTORY_SEPARATOR . CH::CONFIG;
        $file = new Json($path);

        return $this->setupConfig($file, $settings);
    }

    /**
     * Includes a external captainhook configuration
     *
     * @param  string $path
     * @return \CaptainHook\App\Config
     * @throws \Exception
     */
    private function includeConfig(string $path): Config
    {
        $file = new Json($path);
        if (!$file->exists()) {
            throw new RuntimeException('Config to include not found: ' . $path);
        }
        return $this->setupConfig($file);
    }

    /**
     * Return a configuration with data loaded from json file it it exists
     *
     * @param  \CaptainHook\App\Storage\File\Json $file
     * @param  array                              $settings
     * @return \CaptainHook\App\Config
     * @throws \Exception
     */
    private function setupConfig(Json $file, array $settings = []): Config
    {
        return $file->exists()
            ? $this->loadConfigFromFile($file, $settings)
            : new Config($file->getPath(), false, $settings);
    }

    /**
     * Loads a given file into given the configuration
     *
     * @param  \CaptainHook\App\Storage\File\Json $file
     * @param  array                              $settings
     * @return \CaptainHook\App\Config
     * @throws \Exception
     */
    private function loadConfigFromFile(Json $file, array $settings): Config
    {
        $json = $file->readAssoc();
        Util::validateJsonConfiguration($json);

        $settings = array_merge($this->extractSettings($json), $settings);
        $config   = new Config($file->getPath(), true, $settings);

        $this->appendIncludedConfigurations($config, $json);

        foreach (HookUtil::getValidHooks() as $hook => $class) {
            if (isset($json[$hook])) {
                $this->configureHook($config->getHookConfig($hook), $json[$hook]);
            }
        }
        return $config;
    }

    /**
     * Return `config` section of captainhook.json
     *
     * @param  array $json
     * @return array
     */
    private function extractSettings(array $json): array
    {
        return isset($json['config']) && is_array($json['config']) ? $json['config'] : [];
    }

    /**
     * Setup a hook configuration by json data
     *
     * @param  \CaptainHook\App\Config\Hook $config
     * @param  array                        $json
     * @return void
     * @throws \Exception
     */
    private function configureHook(Config\Hook $config, array $json): void
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
     * @param  \CaptainHook\App\Config $config
     * @param  array                   $json
     * @throws \Exception
     */
    private function appendIncludedConfigurations(Config $config, array $json)
    {
        $this->readMaxIncludeLevel($json);
        if ($this->includeLevel < $this->maxIncludeLevel) {
            $includes = $this->loadIncludedConfigs($json, $config->getPath());
            foreach (HookUtil::getValidHooks() as $hook => $class) {
                $this->mergeHookConfigFromIncludes($config->getHookConfig($hook), $includes);
            }
        }
        $this->includeLevel++;
    }

    /**
     * Check config section for 'includes-level' setting
     *
     * @param array $json
     */
    private function readMaxIncludeLevel(array $json): void
    {
        // read the include level setting only for the actual configuration
        if ($this->includeLevel === 0 && isset($json['config'][Config::SETTING_INCLUDES_LEVEL])) {
            $this->maxIncludeLevel = (int) $json['config'][Config::SETTING_INCLUDES_LEVEL];
        }
    }

    /**
     * Merge a given hook config with the corresponding hook configs from a list of included configurations
     *
     * @param  \CaptainHook\App\Config\Hook $hook
     * @param  \CaptainHook\App\Config[]    $includes
     * @return void
     */
    private function mergeHookConfigFromIncludes(Hook $hook, array $includes): void
    {
        foreach ($includes as $includedConfig) {
            $includedHook = $includedConfig->getHookConfig($hook->getName());
            if ($includedHook->isEnabled()) {
                $hook->setEnabled(true);
                $this->copyActionsFromTo($includedHook, $hook);
            }
        }
    }

    /**
     * Return list of included configurations to add them to the main configuration afterwards
     *
     * @param  array  $json
     * @param  string $path
     * @return \CaptainHook\App\Config[]
     * @throws \Exception
     */
    protected function loadIncludedConfigs(array $json, string $path): array
    {
        $includes  = [];
        $directory = dirname($path);
        $files     = isset($json['config'][Config::SETTING_INCLUDES])
                     && is_array($json['config'][Config::SETTING_INCLUDES])
                   ? $json['config'][Config::SETTING_INCLUDES]
                   : [];

        foreach ($files as $file) {
            $includes[] = $this->includeConfig($directory . DIRECTORY_SEPARATOR . $file);
        }
        return $includes;
    }

    /**
     * Copy action from a given configuration to the second given configuration
     *
     * @param \CaptainHook\App\Config\Hook $sourceConfig
     * @param \CaptainHook\App\Config\Hook $targetConfig
     */
    private function copyActionsFromTo(Hook $sourceConfig, Hook $targetConfig)
    {
        foreach ($sourceConfig->getActions() as $action) {
            $targetConfig->addAction($action);
        }
    }

    /**
     * Config factory method
     *
     * @param  string $path
     * @param  array  $settings
     * @return \CaptainHook\App\Config
     * @throws \Exception
     */
    public static function create(string $path = '', array $settings = []): Config
    {
        $factory = new static();
        return $factory->createConfig($path, $settings);
    }
}
