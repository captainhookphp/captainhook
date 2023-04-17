<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App;

use InvalidArgumentException;
use SebastianFeldmann\Camino\Check;

/**
 * Class Config
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Config
{
    public const SETTING_ALLOW_FAILURE       = 'allow-failure';
    public const SETTING_BOOTSTRAP           = 'bootstrap';
    public const SETTING_COLORS              = 'ansi-colors';
    public const SETTING_CUSTOM              = 'custom';
    public const SETTING_GIT_DIR             = 'git-directory';
    public const SETTING_INCLUDES            = 'includes';
    public const SETTING_INCLUDES_LEVEL      = 'includes-level';
    public const SETTING_LABEL               = 'label';
    public const SETTING_RUN_EXEC            = 'run-exec';
    public const SETTING_RUN_MODE            = 'run-mode';
    public const SETTING_RUN_PATH            = 'run-path';
    public const SETTING_PHP_PATH            = 'php-path';
    public const SETTING_VERBOSITY           = 'verbosity';
    public const SETTING_FAIL_ON_FIRST_ERROR = 'fail-on-first-error';

    /**
     * Path to the config file
     *
     * @var string
     */
    private $path;

    /**
     * Does the config file exist
     *
     * @var bool
     */
    private $fileExists;

    /**
     * CaptainHook settings
     *
     * @var array<string, string>
     */
    private $settings;

    /**
     * List of users custom settings
     *
     * @var array<string, mixed>
     */
    private $custom = [];

    /**
     * List of plugins
     *
     * @var array<string, \CaptainHook\App\Config\Plugin>
     */
    private $plugins = [];

    /**
     * List of hook configs
     *
     * @var array<string, \CaptainHook\App\Config\Hook>
     */
    private $hooks = [];

    /**
     * Config constructor
     *
     * @param string               $path
     * @param bool                 $fileExists
     * @param array<string, mixed> $settings
     */
    public function __construct(string $path, bool $fileExists = false, array $settings = [])
    {
        $settings = $this->setupPlugins($settings);
        $settings = $this->setupCustom($settings);

        $this->path       = $path;
        $this->fileExists = $fileExists;
        $this->settings   = $settings;

        foreach (Hooks::getValidHooks() as $hook => $value) {
            $this->hooks[$hook] = new Config\Hook($hook);
        }
    }

    /**
     * Extract custom settings from Captain Hook ones
     *
     * @param  array<string, mixed> $settings
     * @return array<string, mixed>
     */
    private function setupCustom(array $settings): array
    {
        /* @var array<string, mixed> $custom */
        $this->custom = $settings['custom'] ?? [];
        unset($settings['custom']);

        return $settings;
    }

    /**
     * Setup all configured plugins
     *
     * @param  array<string, mixed> $settings
     * @return array<string, mixed>
     */
    private function setupPlugins(array $settings): array
    {
        /* @var array<int, array<string, mixed>> $pluginSettings */
        $pluginSettings = $settings['plugins'] ?? [];
        unset($settings['plugins']);

        foreach ($pluginSettings as $plugin) {
            $name                 = (string) $plugin['plugin'];
            $options              = isset($plugin['options']) && is_array($plugin['options'])
                ? $plugin['options']
                : [];
            $this->plugins[$name] = new Config\Plugin($name, $options);
        }
        return $settings;
    }

    /**
     * Is configuration loaded from file
     *
     * @return bool
     */
    public function isLoadedFromFile(): bool
    {
        return $this->fileExists;
    }

    /**
     * Are actions allowed to fail without stopping the git operation
     *
     * @return bool
     */
    public function isFailureAllowed(): bool
    {
        return (bool) ($this->settings[self::SETTING_ALLOW_FAILURE] ?? false);
    }

    /**
     * @param  string $hook
     * @param  bool   $withVirtual if true, also check if hook is enabled through any enabled virtual hook
     * @return bool
     */
    public function isHookEnabled(string $hook, bool $withVirtual = true): bool
    {
        // either this hook is explicitly enabled
        $hookConfig = $this->getHookConfig($hook);
        if ($hookConfig->isEnabled()) {
            return true;
        }

        // or any virtual hook that triggers it is enabled
        if ($withVirtual && Hooks::triggersVirtualHook($hookConfig->getName())) {
            $virtualHookConfig = $this->getHookConfig(Hooks::getVirtualHook($hookConfig->getName()));
            if ($virtualHookConfig->isEnabled()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Path getter
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Return git directory path if configured, CWD/.git if not
     *
     * @return string
     */
    public function getGitDirectory(): string
    {
        if (empty($this->settings[self::SETTING_GIT_DIR])) {
            return getcwd() . '/.git';
        }

        // if repo path is absolute use it otherwise create an absolute path relative to the configuration file
        return Check::isAbsolutePath($this->settings[self::SETTING_GIT_DIR])
            ? $this->settings[self::SETTING_GIT_DIR]
            : dirname($this->path) . '/' . $this->settings[self::SETTING_GIT_DIR];
    }

    /**
     * Return bootstrap file if configured, CWD/vendor/autoload.php by default
     *
     * @return string
     */
    public function getBootstrap(): string
    {
        return !empty($this->settings[self::SETTING_BOOTSTRAP])
            ? $this->settings[self::SETTING_BOOTSTRAP]
            : 'vendor/autoload.php';
    }

    /**
     * Return the configured verbosity
     *
     * @return string
     */
    public function getVerbosity(): string
    {
        return !empty($this->settings[self::SETTING_VERBOSITY])
            ? $this->settings[self::SETTING_VERBOSITY]
            : 'normal';
    }

    /**
     * Should the output use ansi colors
     *
     * @return bool
     */
    public function useAnsiColors(): bool
    {
        return (bool) ($this->settings[self::SETTING_COLORS] ?? true);
    }

    /**
     * Get configured run-mode
     *
     * @return string
     */
    public function getRunMode(): string
    {
        return (string) ($this->settings[self::SETTING_RUN_MODE] ?? 'local');
    }

    /**
     * Get configured run-exec
     *
     * @return string
     */
    public function getRunExec(): string
    {
        return (string) ($this->settings[self::SETTING_RUN_EXEC] ?? '');
    }

    /**
     * Get configured run-path
     *
     * @return string
     */
    public function getRunPath(): string
    {
        return (string) ($this->settings[self::SETTING_RUN_PATH] ?? '');
    }

    /**
     * Get configured php-path
     *
     * @return string
     */
    public function getPhpPath(): string
    {
        return (string) ($this->settings[self::SETTING_PHP_PATH] ?? '');
    }

    /**
     * Returns the users custom config values
     *
     * @return array<string, mixed>
     */
    public function getCustomSettings(): array
    {
        return $this->custom;
    }

    /**
     * Whether to abort the hook as soon as a any action has errored. Default is true.
     * Otherwise, all actions get executed (even if some of them have failed) and
     * finally, a non-zero exit code is returned if any action has errored.
     *
     * @return bool
     */
    public function failOnFirstError(): bool
    {
        return (bool) ($this->settings[self::SETTING_FAIL_ON_FIRST_ERROR] ?? true);
    }

    /**
     * Return config for given hook
     *
     * @param  string $hook
     * @return \CaptainHook\App\Config\Hook
     * @throws \InvalidArgumentException
     */
    public function getHookConfig(string $hook): Config\Hook
    {
        if (!Hook\Util::isValid($hook)) {
            throw new InvalidArgumentException('Invalid hook name: ' . $hook);
        }
        return $this->hooks[$hook];
    }

    /**
     * Return plugins
     *
     * @return Config\Plugin[]
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * Return config array to write to disc
     *
     * @return array<mixed>
     */
    public function getJsonData(): array
    {
        $data = [];
        // only append config settings if at least one setting is present
        if (!empty($this->settings)) {
            $data['config'] = $this->settings;
        }

        foreach ($this->plugins as $plugin) {
            $data['config']['plugins'][] = $plugin->getJsonData();
        }

        // append all configured hooks
        foreach (Hooks::getValidHooks() as $hook => $value) {
            $data[$hook] = $this->hooks[$hook]->getJsonData();
        }

        return $data;
    }
}
