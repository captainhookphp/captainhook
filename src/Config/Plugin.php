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

use CaptainHook\App\Exception\InvalidPlugin;
use CaptainHook\App\Plugin\CaptainHook;

/**
 * Class Plugin
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.9.0
 */
class Plugin
{
    /**
     * Plugin class
     *
     * @var string
     */
    private $pluginClass;

    /**
     * Plugin instance
     *
     * @var CaptainHook
     */
    private $plugin;

    /**
     * Map of options name => value
     *
     * @var Options
     */
    private $options;

    /**
     * Plugin constructor
     *
     * @param string $pluginClass
     * @param array $options
     */
    public function __construct(string $pluginClass, array $options = [])
    {
        if (!is_a($pluginClass, CaptainHook::class, true)) {
            throw new InvalidPlugin("{$pluginClass} is not a valid CaptainHook plugin.");
        }

        $this->pluginClass = $pluginClass;
        $this->plugin = new $pluginClass;
        $this->setupOptions($options);
    }

    /**
     * Setup options
     *
     * @param array $options
     */
    private function setupOptions(array $options): void
    {
        $this->options = new Options($options);
    }

    /**
     * Plugin class name getter
     *
     * @return string
     */
    public function getPluginClass(): string
    {
        return $this->pluginClass;
    }

    /**
     * Return the plugin instance
     *
     * @return CaptainHook
     */
    public function getPlugin(): CaptainHook
    {
        return $this->plugin;
    }

    /**
     * Return option map
     *
     * @return Options
     */
    public function getOptions(): Options
    {
        return $this->options;
    }

    /**
     * Return config data
     *
     * @return array
     */
    public function getJsonData(): array
    {
        return [
            'plugin' => $this->pluginClass,
            'options' => $this->options->getAll(),
        ];
    }
}
