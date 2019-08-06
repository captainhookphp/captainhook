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
use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Hook\Util;
use SebastianFeldmann\Git\Repository;
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
abstract class Hook extends Base
{
    /**
     * Hook name [pre-commit|pre-push|...]
     *
     * @var string
     */
    protected $name;

    /**
     * Path to the configuration file to use
     *
     * @var string
     */
    protected $configFile;

    /**
     * Path to the git repository to use
     *
     * @var string
     */
    protected $repositoryPath;

    /**
     * Hook constructor
     *
     * @param string $configFile
     * @param string $repositoryPath
     */
    public function __construct(string $configFile, string $repositoryPath)
    {
        $this->configFile     = $configFile;
        $this->repositoryPath = $repositoryPath;
        parent::__construct();
    }

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setName($this->name)
             ->setDescription('Run git ' . $this->name . ' hook.')
             ->setHelp('This command executes the ' . $this->name . ' hook.');
    }

    /**
     * Execute the command
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io         = $this->getIO($input, $output);
        $config     = $this->getConfig($this->configFile, true);
        $repository = new Repository($this->repositoryPath);

        // use ansi coloring only if not disabled in captainhook.json
        $output->setDecorated($config->useAnsiColors());
        // use the configured verbosity to manage general output verbosity
        $output->setVerbosity(IOUtil::mapConfigVerbosity($config->getVerbosity()));

        /** @var \CaptainHook\App\Runner\Hook $hook */
        $class = '\\CaptainHook\\App\\Runner\\Hook\\' . Util::getHookCommand($this->name);
        $hook  = new $class($io, $config, $repository);

        $hook->run();
        return 0;
    }
}
