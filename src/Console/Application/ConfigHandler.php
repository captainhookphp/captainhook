<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Console\Application;

use HookMeUp\App\Console\Application;

/**
 * Class ConfigHandler
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
abstract class ConfigHandler extends Application
{
    /**
     * Path to HookMeUp config file
     *
     * @var string
     */
    protected $configFile;

    /**
     * Set the configuration file to use.
     *
     * @param  string $config
     */
    public function setConfigFile($config)
    {
        $this->configFile = $config;
    }

    /**
     * Get the configuration file to use.
     *
     * @return string
     */
    public function getConfigFile()
    {
        if (null === $this->configFile) {
            $this->configFile = getcwd() . DIRECTORY_SEPARATOR . 'hookmeup.json';
        }
        return $this->configFile;
    }
}
