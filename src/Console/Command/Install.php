<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console\Command;

use SebastianFeldmann\CaptainHook\Runner\Installer;
use SebastianFeldmann\Git\Repository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Install
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Install extends Base
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('install')
             ->setDescription('Install git hooks')
             ->setHelp('This command will install the git hooks to your .git directory')
             ->addArgument('hook', InputArgument::OPTIONAL, 'Hook you want to install')
             ->addOption(
                 'configuration',
                 'c',
                 InputOption::VALUE_OPTIONAL,
                 'Path to your json configuration',
                 getcwd() . DIRECTORY_SEPARATOR . 'captainhook.json'
             )->addOption('force', 'f', InputOption::VALUE_NONE, 'Force to overwrite existing hooks')
             ->addOption(
                 'git-directory',
                 'g',
                 InputOption::VALUE_OPTIONAL,
                 'Path to your .git directory',
                 getcwd() . DIRECTORY_SEPARATOR . '.git'
             );
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @throws \SebastianFeldmann\CaptainHook\Exception\InvalidHookName
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io     = $this->getIO($input, $output);
        $config = $this->getConfig($input->getOption('configuration'), true);
        $repo   = new Repository(dirname($input->getOption('git-directory')));

        $installer = new Installer($io, $config, $repo);
        $installer->setForce($input->getOption('force'))
                  ->setHook((string) $input->getArgument('hook'));

        $installer->run();
    }
}
