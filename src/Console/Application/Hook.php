<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Application;

use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Util;
use CaptainHook\App\Console\Command;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Hook extends ConfigHandler
{
    /**
     * Path to the git repository
     *
     * @var string
     */
    protected $repositoryPath = '';

    /**
     * Hook that gets executed
     *
     * @var string
     */
    protected $hookToExecute  = '';

    /**
     * Repository path setter
     *
     * @param  string $git
     * @return void
     */
    public function setRepositoryPath(string $git) : void
    {
        $this->repositoryPath = $git;
    }

    /**
     * Get the git repository root path
     *
     * @return string
     */
    public function getRepositoryPath() : string
    {
        if (empty($this->repositoryPath)) {
            $this->repositoryPath = (string) getcwd();
        }
        return $this->repositoryPath;
    }

    /**
     * Set the hook to execute
     *
     * @param  string $hook
     * @return \CaptainHook\App\Console\Application\Hook
     */
    public function setHook(string $hook) : Hook
    {
        if (!Util::isValid($hook)) {
            throw new \RuntimeException('Invalid hook name');
        }
        $this->hookToExecute = $hook;
        return $this;
    }

    /**
     * Execute hook
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function doRun(InputInterface $input, OutputInterface $output) : int
    {
        $input->setInteractive(false);

        $command = $this->createCommand();

        try {
            return $command->run($input, $output);
        } catch (ActionFailed $e) {
            if ($output->isDebug()) {
                throw $e;
            }
            return $this->handleError($output, $e);
        }
    }

    /**
     * Create the hook command
     *
     * @return \CaptainHook\App\Console\Command\Hook
     */
    private function createCommand() : Command\Hook
    {
        /* @var \CaptainHook\App\Console\Command\Hook $command */
        $class   = '\\CaptainHook\\App\\Console\\Command\\Hook\\' . Util::getHookCommand($this->hookToExecute);
        $command = new $class($this->getConfigFile(), $this->getRepositoryPath());
        $command->setHelperSet($this->getHelperSet());

        return $command;
    }

    /**
     * Handle all hook errors
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @param  \Exception                                        $e
     * @return int
     */
    private function handleError(OutputInterface $output, Exception $e): int
    {
        $error = '
                  _______________________________________
                 /                                       \
                 | ARRRRR! Hook execution failed!         |
                 |                                        |
                /  Your git command did not went through! | 
               /_                                         |  
       /(o)\     | For further details check the output   |
      /  ()/ /)  | or run CaptainHook in verbose or debug |
     /.;.))\'".)  | mode.                                  |
     //////.-\'   \_______________________________________/
=====))=))===()  
  ///\'
 //
  \'';
        $output->writeLn('<error>' . $error . '</error>');

        if ($output->isVerbose()) {
            $output->writeLn(
                [
                    '',
                    IOUtil::getLineSeparator(8)
                      . ' Error details: <comment>Exception</comment> '
                      . IOUtil::getLineSeparator(46),
                    $e->getMessage(),
                    ''
                ]
            );
        }
        return 1;
    }
}
