<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hooks;
use Exception;
use RuntimeException;

/**
 * Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Hook extends RepositoryAware
{
    /**
     * Hook that should be handled.
     *
     * @var string
     */
    protected $hook;

    /**
     * Execute stuff before executing any actions
     *
     * @return void
     */
    public function beforeHook(): void
    {
        // empty template method
    }

    /**
     * Execute stuff before every actions
     *
     * @return void
     */
    public function beforeAction(): void
    {
        // empty template method
    }

    /**
     * Execute stuff after every actions
     *
     * @return void
     */
    public function afterAction(): void
    {
        //empty template method
    }

    /**
     * Execute stuff after all actions
     *
     * @return void
     */
    public function afterHook(): void
    {
        // empty template method
    }

    /**
     * Execute the hook and all its actions
     *
     * @return void
     * @throws \Exception
     */
    public function run(): void
    {
        $hookConfig = $this->config->getHookConfig($this->hook);

        // if hook is not enabled in captainhook configuration skip the execution
        if (!$hookConfig->isEnabled()) {
            $this->io->write($this->formatHookHeadline('Skip'), true, IO::VERBOSE);
            return;
        }

        $this->io->write($this->formatHookHeadline('Execute'), true, IO::VERBOSE);

        $actions = $this->getActionsToExecute($hookConfig);

        // if no actions are configured do nothing
        if (count($actions) === 0) {
            $this->io->write(['', '<info>No actions to execute</info>'], true, IO::VERBOSE);
            return;
        }
        $this->beforeHook();
        $this->executeActions($actions);
        $this->afterHook();
    }

    /**
     * Return all the actions to execute
     *
     * Returns all actions from the triggered hook but also any actions of virtual hooks that might be triggered.
     * E.g. 'post-rewrite' or 'post-checkout' trigger the virtual/artificial 'post-change' hook.
     * Virtual hooks are special hooks to simplify configuration.
     *
     * @param  \CaptainHook\App\Config\Hook $hookConfig
     * @return \CaptainHook\App\Config\Action[]
     */
    private function getActionsToExecute(Config\Hook $hookConfig)
    {
        $actions = $hookConfig->getActions();
        if (!Hooks::triggersVirtualHook($hookConfig->getName())) {
            return $actions;
        }

        $virtualHookConfig = $this->config->getHookConfig(Hooks::getVirtualHook($hookConfig->getName()));
        if (!$virtualHookConfig->isEnabled()) {
            return $actions;
        }
        return array_merge($actions, $virtualHookConfig->getActions());
    }

    /**
     * Executes all the Actions configured for the hook
     *
     * @param  \CaptainHook\App\Config\Action[] $actions
     * @throws \Exception
     */
    private function executeActions(array $actions): void
    {
        if ($this->config->failOnFirstError()) {
            $this->executeFailOnFirstError($actions);
        } else {
            $this->executeFailAfterAllActions($actions);
        }
    }

    /**
     * Executes all actions and fails at the first error
     *
     * @param  \CaptainHook\App\Config\Action[] $actions
     * @return void
     * @throws \Exception
     */
    private function executeFailOnFirstError(array $actions): void
    {
        foreach ($actions as $action) {
            $this->handleAction($action);
        }
    }

    /**
     * Executes all actions but does not fail immediately
     *
     * @param \CaptainHook\App\Config\Action[] $actions
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    private function executeFailAfterAllActions(array $actions): void
    {
        $failedActions = 0;

        foreach ($actions as $action) {
            try {
                $this->handleAction($action);
            } catch (Exception $exception) {
                $this->io->write($exception->getMessage());
                $failedActions++;
            }
        }

        if ($failedActions > 0) {
            throw new ActionFailed($failedActions . ' action(s) failed; please see above error messages');
        }
    }

    /**
     * Executes a configured hook action
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @return void
     * @throws \Exception
     */
    private function handleAction(Config\Action $action): void
    {
        if (!$this->doConditionsApply($action->getConditions())) {
            $this->io->write(['', 'Action: <comment>' . $action->getAction() . '</comment>'], true, IO::VERBOSE);
            $this->io->write('Skipped due to unfulfilled conditions', true, IO::VERBOSE);
            return;
        }

        $this->io->write(['', 'Action: <comment>' . $action->getAction() . '</comment>'], true);

        $execMethod = self::getExecMethod(Util::getExecType($action->getAction()));
        $this->{$execMethod}($action);
    }

    /**
     * Execute a php hook action
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    private function executePhpAction(Config\Action $action): void
    {
        $this->beforeAction();
        $runner = new Action\PHP($this->hook);
        $runner->execute($this->config, $this->io, $this->repository, $action);
        $this->afterAction();
    }

    /**
     * Execute a cli hook action
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    private function executeCliAction(Config\Action $action): void
    {
        // since the cli has no straight way to communicate back to php
        // cli hooks have to handle sync stuff by them self
        // so no 'beforeAction' or 'afterAction' is called here
        $runner = new Action\Cli();
        $runner->execute($this->io, $this->repository, $action);
    }

    /**
     * Return the right method name to execute an action
     *
     * @param  string $type
     * @return string
     */
    public static function getExecMethod(string $type): string
    {
        $valid = ['php' => 'executePhpAction', 'cli' => 'executeCliAction'];

        if (!isset($valid[$type])) {
            throw new RuntimeException('invalid action type: ' . $type);
        }
        return $valid[$type];
    }

    /**
     * Check if conditions apply
     *
     * @param  \CaptainHook\App\Config\Condition[] $conditions
     * @return bool
     */
    private function doConditionsApply(array $conditions): bool
    {
        $conditionRunner = new Condition($this->io, $this->repository, $this->hook);
        foreach ($conditions as $config) {
            if (!$conditionRunner->doesConditionApply($config)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Some fancy output formatting
     *
     * @param  string $mode
     * @return string[]
     */
    private function formatHookHeadline(string $mode): array
    {
        $headline = ' ' . $mode . ' hook: <comment>' . $this->hook . '</comment> ';
        return [
            '',
            IOUtil::getLineSeparator(8) .
            $headline .
            IOUtil::getLineSeparator(80 - 8 - mb_strlen(strip_tags($headline)))
        ];
    }
}
