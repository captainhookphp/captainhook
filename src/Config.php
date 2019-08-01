<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App;

use InvalidArgumentException;

/**
 * Class Config
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 * @internal
 */
class Config
{
    const SETTING_GIT_DIR  = 'git-directory';
    const SETTING_COLORS   = 'ansi-colors';
    const SETTING_RUN_MODE = 'run-mode';
    const SETTING_RUN_EXEC = 'run-exec';

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
     * @var array
     */
    private $settings;

    /**
     * List of hook configs
     *
     * @var \CaptainHook\App\Config\Hook[]
     */
    private $hooks = [];

    /**
     * Config constructor
     *
     * @param string $path
     * @param bool   $fileExists
     * @param array  $settings
     */
    public function __construct(string $path, bool $fileExists = false, array $settings = [])
    {
        $this->path       = $path;
        $this->fileExists = $fileExists;
        $this->settings   = $settings;

        foreach (Hooks::getValidHooks() as $hook => $value) {
            $this->hooks[$hook] = new Config\Hook($hook);
        }
    }

    /**
     * Is configuration loaded from file
     *
     * @return bool
     */
    public function isLoadedFromFile() : bool
    {
        return $this->fileExists;
    }

    /**
     * Path getter
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Return git directory path if configured, CWD/.git if not
     *
     * @return string
     */
    public function getGitDirectory() : string
    {
        return !empty($this->settings[self::SETTING_GIT_DIR])
            ? dirname($this->path) . DIRECTORY_SEPARATOR . $this->settings[self::SETTING_GIT_DIR]
            : getcwd() . DIRECTORY_SEPARATOR . '.git';
    }

    /**
     * Should the output use ansi colors
     *
     * @return bool
     */
    public function useAnsiColors() : bool
    {
        return (bool) ($this->settings[self::SETTING_COLORS] ?? true);
    }

    /**
     * Get configured run-mode
     *
     * @return string
     */
    public function getRunMode() : string
    {
        return (string) ($this->settings[self::SETTING_RUN_MODE] ?? 'local');
    }

    /**
     * Get configured run-exec
     *
     * @return string
     */
    public function getRunExec() : string
    {
        return (string) ($this->settings[self::SETTING_RUN_EXEC] ?? '');
    }

    /**
     * Return config for given hook
     *
     * @param  string $hook
     * @return \CaptainHook\App\Config\Hook
     * @throws \InvalidArgumentException
     */
    public function getHookConfig(string $hook) : Config\Hook
    {
        if (!Hook\Util::isValid($hook)) {
            throw new InvalidArgumentException('Invalid hook name: ' . $hook);
        }
        return $this->hooks[$hook];
    }

    /**
     * Return config array to write to disc
     *
     * @return array
     */
    public function getJsonData() : array
    {
        $return = [];

        foreach (Hooks::getValidHooks() as $hook => $value) {
            $return[$hook] = $this->hooks[$hook]->getJsonData();
        }

        return $return;
    }
}
