<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Console\Command;

use CaptainHook\Git\Repository;
use CaptainHook\Runner\Configurator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Config
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Configuration extends Base
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('configure')
             ->setDescription('Configure your hooks.')
             ->setHelp('This command creates or updates your captainhook configuration.')
             ->addOption('extend', 'e', InputOption::VALUE_NONE, 'Extend existing configuration file')
             ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing configuration file')
             ->addOption(
                 'configuration',
                 'c',
                 InputOption::VALUE_OPTIONAL,
                 'Path to your json configuration',
                 getcwd() . DIRECTORY_SEPARATOR . 'captainhook.json'
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
        $config = $this->getConfig($input->getOption('configuration'));
        $repo   = new Repository();

        $configurator = new Configurator($io, $config, $repo);
        $configurator->force($input->getOption('force'))
                     ->extend($input->getOption('extend'))
                     ->run();
    }
}
