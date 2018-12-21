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
    protected $hook;

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
        $hookConfig = $this->config->getHookConfig($this->hook);

        // if hook is not enabled in captainhook.json skip action execution
        if (!$hookConfig->isEnabled()) {
            $this->io->write('<info>skip hook:</info> <comment>' . $this->hook . '</comment>');
            return;
        }

        $this->io->write(['', '<info>execute hook:</info> <comment>' . $this->hook . '</comment>']);
        foreach ($hookConfig->getActions() as $action) {
            $this->handleAction($action);
        }
        $this->afterHook();
    }

    /**
     * Executes a configured hook action
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @throws \Exception
     */
    protected function handleAction(Config\Action $action)
    {
        $this->io->write([
            '',
            'Action: <comment>' . $action->getAction() . '</comment>',
            IOUtil::getLineSeparator()
        ]);

        $execMethod = self::getExecMethod($action->getType());
        $this->{$execMethod}($action);

        $this->io->write([str_repeat('-', 80)]);
    }

    /**
     * Execute a php hook action
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function executePhpAction(Config\Action $action)
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
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function executeCliAction(Config\Action $action)
    {
        // since the cli has no straight way to communicate back to php
        // cli hooks have to handle sync stuff by them self
        // so no 'beforeAction' or 'afterAction' is called here
        $runner = new Action\Cli();
        $runner->execute($this->io, $action, $this->arguments);
    }

    /**
     * Return the right method to call to execute an action
     *
     * @param  string $type
     * @return string
     */
    public static function getExecMethod(string $type) : string
    {
        $valid = ['php' => 'executePhpAction', 'cli' => 'executeCliAction'];

        if (!isset($valid[$type])) {
            throw new \RuntimeException('invalid action type: ' . $type);
        }
        return $valid[$type];
    }
}
