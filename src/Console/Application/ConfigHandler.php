<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console\Application;

use SebastianFeldmann\CaptainHook\Console\Application;

/**
 * Class ConfigHandler
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class ConfigHandler extends Application
{
    /**
     * Path to CaptainHook config file
     *
     * @var string
     */
    protected $configFile;

    /**
     * Set the configuration file to use.
     *
     * @param string $config
     */
    public function setConfigFile(string $config)
    {
        $this->configFile = $config;
    }

    /**
     * Get the configuration file to use.
     *
     * @return string
     */
    public function getConfigFile() : string
    {
        if (empty($this->configFile)) {
            $this->configFile = getcwd() . DIRECTORY_SEPARATOR . 'captainhook.json';
        }
        return $this->configFile;
    }
}
