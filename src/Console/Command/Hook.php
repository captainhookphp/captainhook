<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Command;

use CaptainHook\App\Config;
use CaptainHook\App\Git;
use CaptainHook\App\Runner;
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
abstract class Hook extends Base
{
    /**
     * Hook to execute
     *
     * @var string
     */
    protected $hookName;

    /**
     * Path to the configuration file to use.
     *
     * @var string
     */
    protected $configFile;

    /**
     * Path to the git repository to use.
     *
     * @var string
     */
    protected $repositoryPath;

    /**
     * Hook constructor.
     *
     * @param string $configFile
     * @param string $repositoryPath
     */
    public function __construct($configFile, $repositoryPath)
    {
        $this->configFile     = $configFile;
        $this->repositoryPath = $repositoryPath;
        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName($this->hookName)
             ->setDescription('Run git ' . $this->hookName . ' hook.')
             ->setHelp('This command executes the ' . $this->hookName . ' hook.');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io         = $this->getIO($input, $output);
        $config     = $this->getConfig($this->configFile, true);
        $repository = new Git\Repository($this->repositoryPath);

        // handle command specific setup
        $this->setup($input, $output, $config, $repository);

        // execute the hook
        $hook = new Runner\Hook($io, $config, $repository);
        $hook->setHook($this->hookName);
        $hook->run();
    }

    /**
     * Setup the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \CaptainHook\App\Config                                  $config
     * @param \CaptainHook\App\Git\Repository                          $repository
     * @internal param \CaptainHook\App\Console\Command\IO $io
     */
    protected function setup(InputInterface $input, OutputInterface $output, Config $config, Git\Repository $repository)
    {
        // do something fooish
    }
}
