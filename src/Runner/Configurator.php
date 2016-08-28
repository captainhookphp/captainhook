<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Runner;

use HookMeUp\Config;
use HookMeUp\Runner;
use HookMeUp\Storage\File\Json;
use HookMeUp\Hook\Util;

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
    }

    /**
     * Set configuration mode.
     *
     * @param  string $mode
     * @return \HookMeUp\Runner\Configurator
     */
    public function setMode($mode)
    {
        $this->mode = null === $mode ? 'create' : 'extend';
        return $this;
    }

    /**
     * Return config to handle.
     *
     * @return \HookMeUp\Config
     */
    public function getConfigToManipulate()
    {
        return 'extend' === $this->mode ? $this->config : new Config($this->config->getPath());
    }

    /**
     * Configure a hook.
     *
     * @param \HookMeUp\Config $config
     * @param string           $hook
     */
    public function configureHook(Config $config, $hook)
    {
        $answer = $this->io->ask('    <info>Enable \'' . $hook . '\' hook [y,n]?</info> ', 'n');
        $enable = 'y' === $answer;

        /** @var \HookMeUp\Config\Hook $hookConfig */
        $hookConfig = $config->getHookConfig($hook);
        $hookConfig->setEnabled($enable);

        if ($enable) {
            $addAction = $this->io->ask('    <info>Add a validation action [y,n]?</info> ', 'n');

            while ($addAction == 'y') {
                $hookConfig->addAction($this->getActionConfig());
                // add another action?
                $addAction = $this->io->ask('    <info>Add another validation action [y,n]?</info> ', 'n');
            }
        }
    }

    /**
     * Setup a action config with user input.
     *
     * @return \HookMeUp\Config\Action
     */
    public function getActionConfig()
    {
        $type    = $this->getActionType();
        $msg     = 'php' === $type ? 'PHP class to execute' : 'Script to execute';
        $call    = $this->io->ask('    <info>' . $msg . '?</info> ', '');
        $options = $this->getActionOptions($type);

        return new Config\Action($type, $call, $options);
    }

    /**
     * Ask the user for the action type.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getActionType()
    {
        return $this->io->askAndValidate(
            '    <info>Choose action type [php,cli]?</info> ',
            function($answer) {
                if (!in_array($answer, ['php', 'cli'])) {
                    throw new \RuntimeException('You have to choose either \'php\' or \'cli\'');
                }
                return $answer;
            },
            3,
            ''
        );
    }

    /**
     * Ask the user for any action options.
     *
     * @param  string $type
     * @return array
     */
    public function getActionOptions($type)
    {
        if ('cli' === $type) {
            return [];
        }
        return [];
    }

    /**
     * Write config to project root.
     *
     * @param \HookMeUp\Config $config
     */
    public function writeConfig(Config $config)
    {
        $filePath = $this->config->getPath();
        $file     = new Json($filePath);
        $file->write($config->getJsonData());
    }
}
