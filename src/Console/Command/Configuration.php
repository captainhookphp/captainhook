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
use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Runner\Config\Creator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Config
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Configuration extends Base
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setName('configure')
             ->setDescription('Configure your hooks')
             ->setHelp('This command creates or updates your captainhook configuration')
             ->addOption('extend', 'e', InputOption::VALUE_NONE, 'Extend existing configuration file')
             ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing configuration file')
             ->addOption('advanced', 'a', InputOption::VALUE_NONE, 'More options, but more to type')
             ->addOption(
                 'configuration',
                 'c',
                 InputOption::VALUE_OPTIONAL,
                 'Path to your json configuration',
                 './' . CH::CONFIG
             )
             ->addOption(
                 'vendor-directory',
                 null,
                 InputOption::VALUE_OPTIONAL,
                 'Path to composers vendor directory'
             );
    }

    /**
     * Execute the command
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io       = $this->getIO($input, $output);
        $settings = $this->fetchInputSettings($input, ['vendor-directory']);
        $config   = $this->getConfig(IOUtil::argToString($input->getOption('configuration')), false, $settings);

        $configurator = new Creator($io, $config);
        $configurator->force(IOUtil::argToBool($input->getOption('force')))
                     ->extend(IOUtil::argToBool($input->getOption('extend')))
                     ->advanced(IOUtil::argToBool($input->getOption('advanced')))
                     ->run();
        return 0;
    }
}
