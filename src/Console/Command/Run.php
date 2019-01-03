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

use CaptainHook\App\CH;
use CaptainHook\App\Config;
use CaptainHook\App\Hook\Util;
use SebastianFeldmann\Git\Repository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Run
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Run extends Base
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setName('run')
             ->setDescription('Run a git hook')
             ->setHelp("This command executes a configured git hook")
             ->addArgument('hook', InputArgument::REQUIRED, 'Hook you want to execute')
             ->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'File containing the commit message')
             ->addOption(
                 'configuration',
                 'c',
                 InputOption::VALUE_OPTIONAL,
                 'Path to your json configuration',
                 getcwd() . DIRECTORY_SEPARATOR . CH::CONFIG
             );
    }

    /**
     * Execute the command
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $io     = $this->getIO($input, $output);
        $config = $this->getConfig($input->getOption('configuration'), true);
        $repo   = new Repository();
        $args   = new Config\Options(['file' => $input->getOption('message')]);
        $name   = $input->getArgument('hook');
        $class  = '\\CaptainHook\\App\\Runner\\Hook\\' . Util::getHookCommand($name);

        /** @var \CaptainHook\App\Runner\Hook $hook */
        $hook = new $class($io, $config, $repo, $args);
        $hook->run();
    }
}
