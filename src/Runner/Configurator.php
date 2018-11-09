<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IOUtil;
use SebastianFeldmann\CaptainHook\Hook\Util;
use SebastianFeldmann\CaptainHook\Runner;
use SebastianFeldmann\CaptainHook\Storage\File\Json;

/**
 * Class Configurator
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Configurator extends Runner
{
    /**
     * Force mode
     *
     * @var bool
     */
    private $force;

    /**
     * Extend existing config or create new one
     *
     * @var string
     */
    private $mode;

    /**
     * Use express setup mode
     *
     * @var bool
     */
    private $advanced;

    /**
     * Execute the configurator
     */
    public function run()
    {
        $config = $this->getConfigToManipulate();
        $setup  = $this->getHookSetup();

        $setup->configureHooks($config);
        $this->writeConfig($config);

        $this->io->write(
            [
                '<info>Configuration created successfully</info>',
                'Run <comment>\'vendor/bin/captainhook install\'</comment> to activate your hook configuration',
            ]
        );
    }

    /**
     * Force mode setter
     *
     * @param  bool $force
     * @return \SebastianFeldmann\CaptainHook\Runner\Configurator
     */
    public function force(bool $force) : Configurator
    {
        $this->force = $force;
        return $this;
    }

    /**
     * Set configuration mode
     *
     * @param  bool $extend
     * @return \SebastianFeldmann\CaptainHook\Runner\Configurator
     */
    public function extend(bool $extend) : Configurator
    {
        $this->mode = $extend ? 'extend' : 'create';
        return $this;
    }

    /**
     * Set configuration speed
     *
     * @param  bool $advanced
     * @return \SebastianFeldmann\CaptainHook\Runner\Configurator
     */
    public function advanced(bool $advanced) : Configurator
    {
        $this->advanced = $advanced;
        return $this;
    }

    /**
     * Return config to handle
     *
     * @return \SebastianFeldmann\CaptainHook\Config
     */
    public function getConfigToManipulate() : Config
    {
        // create mode, create blank configuration
        if ('extend' !== $this->mode) {
            // make sure the force option is set if the configuration file exists
            $this->ensureForce();
            return new Config($this->config->getPath());
        }
        return $this->config;
    }

    /**
     * Return the setup handler to ask the user questions
     *
     * @return \SebastianFeldmann\CaptainHook\Runner\Configurator\Setup
     */
    private function getHookSetup()
    {
        return $this->advanced
            ? new Configurator\Setup\Advanced($this->io)
            : new Configurator\Setup\Express($this->io);
    }

    /**
     * Make sure force mode is set if config file exists
     *
     * @throws \RuntimeException
     */
    private function ensureForce()
    {
        if ($this->config->isLoadedFromFile() && !$this->force) {
            throw new \RuntimeException('Configuration file exists, use -f to overwrite, or -e to extend');
        }
    }

    /**
     * Write config to project root
     *
     * @param \SebastianFeldmann\CaptainHook\Config $config
     */
    public function writeConfig(Config $config)
    {
        $filePath = $this->config->getPath();
        $file     = new Json($filePath);
        $file->write($config->getJsonData());
    }
}
