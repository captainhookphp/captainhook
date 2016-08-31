<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Runner;

use HookMeUp\App\Config;
use HookMeUp\App\Console\IOUtil;
use HookMeUp\App\Hook\Util;
use HookMeUp\App\Runner;
use HookMeUp\App\Storage\File\Json;

/**
 * Class Configurator
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
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
     * Execute the configurator.
     */
    public function run()
    {
        $config = $this->getConfigToManipulate();
        foreach (Util::getHooks() as $hook) {
            $this->configureHook($config, $hook);
        }
        $this->writeConfig($config);

        $this->io->write(
            [
                '<info>Configuration created successfully</info>',
                'Run <comment>\'vendor/bin/hookmeup install\'</comment> to activate your hook configuration',
            ]
        );
    }

    /**
     * Force mode setter.
     *
     * @param  bool $force
     * @return \HookMeUp\App\Runner\Configurator
     */
    public function force($force)
    {
        $this->force = $force;
        return $this;
    }

    /**
     * Set configuration mode.
     *
     * @param  bool $extend
     * @return \HookMeUp\App\Runner\Configurator
     */
    public function extend($extend)
    {
        $this->mode = $extend ? 'extend' : 'create';
        return $this;
    }

    /**
     * Return config to handle.
     *
     * @return \HookMeUp\App\Config
     */
    public function getConfigToManipulate()
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
     * Make sure force mode is set if config file exists.
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
     * Configure a hook.
     *
     * @param \HookMeUp\App\Config $config
     * @param string           $hook
     */
    public function configureHook(Config $config, $hook)
    {
        $answer = $this->io->ask('    <info>Enable \'' . $hook . '\' hook [y,n]?</info> ', 'n');
        $enable = IOUtil::answerToBool($answer);

        /** @var \HookMeUp\App\Config\Hook $hookConfig */
        $hookConfig = $config->getHookConfig($hook);
        $hookConfig->setEnabled($enable);

        if ($enable) {
            $addAction = $this->io->ask('    <info>Add a validation action [y,n]?</info> ', 'n');

            while (IOUtil::answerToBool($addAction)) {
                $hookConfig->addAction($this->getActionConfig());
                // add another action?
                $addAction = $this->io->ask('    <info>Add another validation action [y,n]?</info> ', 'n');
            }
        }
    }

    /**
     * Setup a action config with user input.
     *
     * @return \HookMeUp\App\Config\Action
     */
    public function getActionConfig()
    {
        $call    = $this->io->ask('    <info>PHP class or shell command to execute?</info> ', '');
        $type    = Util::getActionType($call);
        $options = $this->getActionOptions($type);

        return new Config\Action($type, $call, $options);
    }

    /**
     * Ask the user for any action options.
     *
     * @param  string $type
     * @return array
     */
    public function getActionOptions($type)
    {
        return 'php' === $type ? $this->getPHPActionOptions() : [];
    }

    /**
     * Get the php action options.
     *
     * @return array
     */
    protected function getPHPActionOptions()
    {
        $options = [];
        $addOption = $this->io->ask('    <info>Add a validator option [y,n]?</info> ', 'n');
        while (IOUtil::answerToBool($addOption)) {
            $options = array_merge($options, $this->getPHPActionOption());
            // add another action?
            $addOption = $this->io->ask('    <info>Add another validator option [y,n]?</info> ', 'n');
        }
        return $options;
    }

    /**
     * Ask the user for a php action option.
     *
     * @return array
     */
    protected function getPHPActionOption()
    {
        $result = [];
        $answer = $this->io->askAndValidate(
            '    <info>Specify options name and value [name:value]</info> ',
            function($value) {
                if (count(explode(':', $value)) !== 2) {
                    throw new \Exception('Invalid option, use "key:value"');
                }
                return $value;
            },
            3,
            null
        );
        if (null !== $answer) {
            list($key, $value) = explode(':', $answer);
            $result = [$key => $value];
        }
        return $result;
    }

    /**
     * Write config to project root.
     *
     * @param \HookMeUp\App\Config $config
     */
    public function writeConfig(Config $config)
    {
        $filePath = $this->config->getPath();
        $file     = new Json($filePath);
        $file->write($config->getJsonData());
    }
}
