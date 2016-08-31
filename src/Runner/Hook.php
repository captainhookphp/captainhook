<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Runner;

/**
 *  Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Hook extends HookHandler
{
    /**
     * Hook config
     *
     * @var \CaptainHook\Config\Hook
     */
    private $hookConfig;

    /**
     * Execute installation.
     */
    public function run()
    {
        /** @var \CaptainHook\Config\Hook $hookConfig */
        $this->hookConfig = $this->config->getHookConfig($this->hookToHandle);

        // execute hooks only if hook is enabled in captainhook.json
        if ($this->hookConfig->isEnabled()) {
            $this->io->write('EXECUTE: ' . $this->hookToHandle);
            foreach ($this->getActionsToRun() as $action) {
                $this->io->write(PHP_EOL . str_repeat('#', 80) . PHP_EOL);
                $runner = $this->getActionRunner($action->getType());
                $runner->execute($this->config, $this->io, $this->repository, $action);
            }
        } else {
            $this->io->write('SKIP: ' . $this->hookToHandle . ' is disabled.');
        }
    }

    /**
     * Return list of actions to run.
     *
     * @return \CaptainHook\Config\Action[]
     */
    protected function getActionsToRun()
    {
        return $this->hookConfig->getActions();
    }

    /**
     * Return matching action runner.
     *
     * @param  string $type
     * @return \CaptainHook\Hook\Action
     * @throws \RuntimeException
     */
    public function getActionRunner($type)
    {
        switch ($type) {
            case 'php':
                return new Action\PHP();
            case 'cli':
                return new Action\Cli();
            default:
                throw new \RuntimeException('Unknown action type: ' . $type);
        }
    }
}
