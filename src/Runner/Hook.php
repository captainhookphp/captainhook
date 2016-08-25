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

use HookMeUp\Runner;
use HookMeUp\Exception;
use HookMeUp\Hook\Util as HookUtil;

/**
 *  Hook
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Hook extends Runner
{
    /**
     * Hook that should be executed
     *
     * @var string
     */
    private $hookToExecute;

    /**
     * @param  string $hook
     * @return \HookMeUp\Runner\Hook
     * @throws \HookMeUp\Exception\InvalidHookName
     */
    public function setHook($hook)
    {
        if (null !== $hook && !HookUtil::isValid($hook)) {
            throw new Exception\InvalidHookName('Invalid hook name \'' . $hook . '\'');
        }
        $this->hookToExecute = $hook;
        return $this;
    }

    /**
     * Execute installation.
     */
    public function run()
    {
        $this->io->write('EXECUTE: ' . $this->hookToExecute);

        foreach ($this->getActionsToRun() as $action) {
            $this->io->write(PHP_EOL . str_repeat('#', 80) . PHP_EOL);
            $runner = $this->getActionRunner($action->getType());
            $runner->execute($this->config, $this->io, $this->repository, $action);
        }
    }

    /**
     * Return list aof actions to run.
     *
     * @return \HookMeUp\Config\Action[]
     */
    protected function getActionsToRun()
    {
        /** @var \HookMeUp\Config\Hook $hookConfig */
        $hookConfig = $this->config->getHookConfig($this->hookToExecute);
        return $hookConfig->getActions();
    }

    /**
     * Return matching action runner.
     *
     * @param  string $type
     * @return \HookMeUp\Runner\Action
     * @throws \RuntimeException
     */
    protected function getActionRunner($type)
    {
        switch ($type) {
            case 'php':
                return new Runner\Action\PHP();
            case 'cli':
                return new Runner\Action\Cli();
            default:
                throw new \RuntimeException('Unknown action type: ' . $type);
        }
    }
}
