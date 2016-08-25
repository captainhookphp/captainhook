<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Command;

use HookMeUp\Git\Repository;
use HookMeUp\Runner\Hook;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Run
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Run extends Base
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('run')
             ->setDescription('Run git hook.')
             ->setHelp("This command executes a configured git hook.")
             ->addArgument('hook', InputArgument::REQUIRED, 'Hook you want to execute.'
             )->addOption(
                'configuration',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Path to your json configuration', getcwd() . DIRECTORY_SEPARATOR . 'hookmeup.json'
             );
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
        $io     = $this->getIO($input, $output);
        $config = $this->getConfig($input->getOption('configuration'), true);
        $repo   = new Repository();

        $hook = new Hook($io, $config, $repo);
        $hook->setHook($input->getArgument('hook'));

        $hook->run();
    }
}
