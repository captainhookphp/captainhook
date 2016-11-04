<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Runner;

use sebastianfeldmann\CaptainHook\Hook\Action as ActionInterface;

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
     * @var \sebastianfeldmann\CaptainHook\Config\Hook
     */
    private $hookConfig;

    /**
     * Execute installation.
     */
    public function run()
    {
        /** @var \sebastianfeldmann\CaptainHook\Config\Hook $hookConfig */
        $this->hookConfig = $this->config->getHookConfig($this->hookToHandle);

        // execute hooks only if hook is enabled in captainhook.json
        if ($this->hookConfig->isEnabled()) {
            $this->io->write('<info>execute hook:</info> <comment>' . $this->hookToHandle . '</comment>');
            foreach ($this->getActionsToRun() as $action) {
                $this->io->write([str_repeat('#', 80), '# <comment>' . $action->getAction() . '</comment>', '']);
                $runner = $this->getActionRunner($action->getType());
                $runner->execute($this->config, $this->io, $this->repository, $action);
            }
        } else {
            $this->io->write('<info>skip hook:</info> <comment>' . $this->hookToHandle . '</comment>');
        }
    }

    /**
     * Return list of actions to run.
     *
     * @return \sebastianfeldmann\CaptainHook\Config\Action[]
     */
    protected function getActionsToRun() : array
    {
        return $this->hookConfig->getActions();
    }

    /**
     * Return matching action runner.
     *
     * @param  string $type
     * @return \sebastianfeldmann\CaptainHook\Hook\Action
     * @throws \RuntimeException
     */
    public function getActionRunner($type) : ActionInterface
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
