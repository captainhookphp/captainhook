<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Application;

use HookMeUp\Console\Application;
use HookMeUp\Hook\Util;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Hook
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Hook extends Application
{
    /**
     * Path to hookmeup config file
     *
     * @var string
     */
    protected $configFile;

    /**
     * Path to the git repository
     *
     * @var string
     */
    protected $repositoryPath;

    /**
     * Hook that gets executed.
     *
     * @var string
     */
    protected $hookToExecute;

    /**
     * Hook to command map
     *
     * @var array
     */
    protected $hookCommandMap = [
        'pre-commit' => 'PreCommit',
        'commit-msg' => 'CommitMsg',
        'pre-push'   => 'PrePush'
    ];

    /**
     * @param  string $config
     * @return \HookMeUp\Console\Application\Hook
     */
    public function useConfigFile($config)
    {
        $this->configFile = $config;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfigFile()
    {
        if (null === $this->configFile) {
            $this->configFile = getcwd() . '/hookmeup.json';
        }
        return $this->configFile;
    }

    /**
     * @param  string $git
     * @return \HookMeUp\Console\Application\Hook
     */
    public function useRepository($git)
    {
        $this->repositoryPath = $git;
        return $this;
    }

    /**
     * @return string
     */
    public function getRepositoryPath()
    {
        if (null === $this->repositoryPath) {
            $this->repositoryPath = getcwd();
        }
        return $this->repositoryPath;
    }

    /**
     * @param  string $hook
     * @return \HookMeUp\Console\Application\Hook
     */
    public function executeHook($hook)
    {
        if (!Util::isValid($hook)) {
            throw new \RuntimeException('Invalid hook name');
        }
        $this->hookToExecute = $hook;
        return $this;
    }

    /**
     * Execute hook.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(false);

        $command = $this->createCommand();
        return $command->run($input, $output);
    }

    /**
     * Create the hook command.
     *
     * @return \HookMeUp\Console\Command\Hook
     */
    private function createCommand()
    {
        /* @var \HookMeUp\Console\Command\Hook $command */
        $class   = '\\HookMeUp\\Console\\Command\\Hook\\' . $this->hookCommandMap[$this->hookToExecute];
        $command = new $class($this->getConfigFile(), $this->getRepositoryPath());
        $command->setHelperSet($this->getHelperSet());

        return $command;
    }
}
