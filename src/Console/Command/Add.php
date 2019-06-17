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
use CaptainHook\App\Runner\Config\Editor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Add
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.2.0
 */
class Add extends Base
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setName('add')
             ->setDescription('Add an action to a hook configuration')
             ->setHelp('This command will add an action configuration to a given hook configuration')
             ->addArgument('hook', InputArgument::REQUIRED, 'Hook you want to add the action to')
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
     * @return int|null
     * @throws \CaptainHook\App\Exception\InvalidHookName
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io     = $this->getIO($input, $output);
        $config = $this->getConfig(IOUtil::argToString($input->getOption('configuration')), true);

        $editor = new Editor($io, $config);
        $editor->setHook(IOUtil::argToString($input->getArgument('hook')))
               ->setChange('AddAction')
               ->run();

        return 0;
    }
}
