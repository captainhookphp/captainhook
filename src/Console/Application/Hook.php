<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Console\Application;

use CaptainHook\Hook\Util;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Hook extends ConfigHandler
{
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
     * Repository path setter.
     *
     * @param string $git
     */
    public function setRepositoryPath($git)
    {
        $this->repositoryPath = $git;
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
     * Set the hook to execute.
     *
     * @param  string $hook
     * @return \CaptainHook\Console\Application\Hook
     */
    public function setHook($hook)
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
     * @return \CaptainHook\Console\Command\Hook
     */
    private function createCommand()
    {
        /* @var \CaptainHook\Console\Command\Hook $command */
        $class   = '\\CaptainHook\\Console\\Command\\Hook\\' . $this->getHookCommand();
        $command = new $class($this->getConfigFile(), $this->getRepositoryPath());
        $command->setHelperSet($this->getHelperSet());

        return $command;
    }

    /**
     * Get the command class name to execute.
     *
     * @return string
     */
    private function getHookCommand()
    {
        if (null === $this->hookToExecute) {
            throw new \RuntimeException('No hook to execute');
        }
        return $this->hookCommandMap[$this->hookToExecute];
    }
}
