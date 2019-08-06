<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Options;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Console\IOUtil;
use SebastianFeldmann\Git\Repository;
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
    public function beforeHook() : void
    {
        // empty template method
    }

    /**
     * Execute stuff before every actions
     *
     * @return void
     */
    public function beforeAction() : void
    {
        // empty template method
    }

    /**
     * Execute stuff after every actions
     *
     * @return void
     */
    public function afterAction() : void
    {
        //empty template method
    }

    /**
     * Execute stuff after all actions
     *
     * @return void
     */
    public function afterHook() : void
    {
        // empty template method
    }

    /**
     * Execute the hook and all its actions
     *
     * @return void
     * @throws \Exception
     */
    public function run() : void
    {
        /** @var \CaptainHook\App\Config\Hook $hookConfig */
        $hookConfig = $this->config->getHookConfig($this->hook);
        $actions    = $hookConfig->getActions();

        // if hook is not enabled in captainhook configuration skip the execution
        if (!$hookConfig->isEnabled()) {
            $this->io->write($this->formatHookHeadline('Skip'), true, IO::VERBOSE);
            return;
        }
        // if no actions are configured do nothing
        if (count($actions) === 0) {
            $this->io->write(['', '<info>No actions to execute</info>'], true, IO::VERBOSE);
            return;
        }

        $this->io->write($this->formatHookHeadline('Execute'), true, IO::VERBOSE);
        $this->beforeHook();
        foreach ($actions as $action) {
            $this->handleAction($action);
        }
        $this->afterHook();
    }

    /**
     * Executes a configured hook action
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @return void
     * @throws \Exception
     */
    protected function handleAction(Config\Action $action) : void
    {
        $this->io->write(['', 'Action: <comment>' . $action->getAction() . '</comment>'], true, IO::VERBOSE);

        if (!$this->doConditionsApply($action->getConditions())) {
            $this->io->write(
                ['', 'Skipped due to failing conditions'],
                true,
                IO::VERBOSE
            );
            return;
        }

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
    protected function executePhpAction(Config\Action $action) : void
    {
        $this->beforeAction();
        $runner = new Action\PHP();
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
    protected function executeCliAction(Config\Action $action) : void
    {
        // since the cli has no straight way to communicate back to php
        // cli hooks have to handle sync stuff by them self
        // so no 'beforeAction' or 'afterAction' is called here
        $runner = new Action\Cli();
        $runner->execute($this->io, $action);
    }

    /**
     * Return the right method name to execute an action
     *
     * @param  string $type
     * @return string
     */
    public static function getExecMethod(string $type) : string
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
    private function doConditionsApply(array $conditions) : bool
    {
        $conditionRunner = new Condition($this->io, $this->repository);
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
    private function formatHookHeadline(string $mode) : array
    {
        $headline = ' ' . $mode . ' hook: <comment>' . $this->hook . '</comment> ';
        return [
            '',
            IOUtil::getLineSeparator(8) .
            $headline .
            IOUtil::getLineSeparator(80 - 8 - strlen(strip_tags($headline)))
        ];
    }
}
