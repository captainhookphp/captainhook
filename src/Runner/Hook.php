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
use CaptainHook\App\Hook\Action as ActionInterface;
use SebastianFeldmann\Git\Repository;

/**
 * Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Hook extends RepositoryAware
{
    /**
     * Hook that should be handled.
     *
     * @var string
     */
    protected $hookName;

    /**
     * List of original hook arguments
     *
     * @var \CaptainHook\App\Config\Options
     */
    protected $arguments;

    /**
     * HookHandler constructor.
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \CaptainHook\App\Config           $config
     * @param \SebastianFeldmann\Git\Repository $repository
     * @param \CaptainHook\App\Config\Options   $arguments
     */
    public function __construct(IO $io, Config $config, Repository $repository, Options $arguments)
    {
        parent::__construct($io, $config, $repository);
        $this->arguments = $arguments;
    }

    /**
     * Execute stuff before executing any actions
     *
     * @return void
     */
    public function beforeHook()
    {
        // empty template method
    }

    /**
     * Execute stuff before every actions
     *
     * @return void
     */
    public function beforeAction()
    {
        // empty template method
    }

    /**
     * Execute stuff after every actions
     *
     * @return void
     */
    public function afterAction()
    {
        //empty template method
    }

    /**
     * Execute stuff after all actions
     *
     * @return void
     */
    public function afterHook()
    {
        // empty template method
    }

    /**
     * Execute installation.
     *
     * @throws \Exception
     */
    public function run()
    {
        $this->beforeHook();
        /** @var \CaptainHook\App\Config\Hook $hookConfig */
        $hookConfig = $this->config->getHookConfig($this->hookName);

        // if hook is not enabled in captainhook.json skip action execution
        if (!$hookConfig->isEnabled()) {
            $this->io->write('<info>skip hook:</info> <comment>' . $this->hookName . '</comment>');
            return;
        }

        $this->io->write(['', '<info>execute hook:</info> <comment>' . $this->hookName . '</comment>']);
        foreach ($hookConfig->getActions() as $action) {
            $this->executeAction($action);
        }
        $this->afterHook();
    }

    /**
     * Executes a configured hook action
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @throws \Exception
     */
    protected function executeAction(Config\Action $action)
    {
        $this->io->write([
            '',
            'Action: <comment>' . $action->getAction() . '</comment>',
            IOUtil::getLineSeparator()
        ]);

        $type   = $action->getType();
        $runner = self::getActionRunner($type);

        $this->beforeAction();
        $runner->execute($this->config, $this->io, $this->repository, $action);

        // execute post handling only for php actions
        // cli actions should do post actions them self
        if ($type === 'php') {
            $this->afterAction();
        }

        $this->io->write([str_repeat('-', 80)]);
    }

    /**
     * Return matching action runner.
     *
     * @param  string $type
     * @return \CaptainHook\App\Hook\Action
     * @throws \RuntimeException
     */
    public static function getActionRunner($type) : ActionInterface
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
