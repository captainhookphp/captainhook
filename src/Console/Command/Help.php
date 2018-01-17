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

use SebastianFeldmann\CaptainHook\CH;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Help
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 2.0.6
 */
class Help extends Base
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    private $command;

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('help')
             ->setDescription('Shows this help message')
             ->setHelp('Shows this help message')
             ->setDefinition([
                new InputArgument('command_name', InputArgument::OPTIONAL, 'The command name', 'help')
             ]);
    }

    /**
     * Set command to get help for.
     *
     * @param \Symfony\Component\Console\Command\Command $command
     */
    public function setCommand(SymfonyCommand $command)
    {
        $this->command = $command;
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
        if ($this->command) {
            $helper = new DescriptorHelper();
            $helper->describe($output, $this->command);
            return;
        }

        $io = $this->getIO($input, $output);
        $io->write([
                '<info>CaptainHook</info> version <comment>' . CH::VERSION . '</comment> ' . CH::RELEASE_DATE,
                '',
                '<comment>Usage:</comment>',
                '  captainhook [command] [options]',
                '',
                '<comment>Available commands:</comment>',
                '  <info>help</info>      Outputs this help message',
                '  <info>configure</info> Create a CaptainHook configuration',
                '  <info>install</info>   Install hooks to your .git/hooks directory',
                '',
                '<comment>Help:</comment>',
                '  Use captainhook [command] --help for further instructions.',
                '',
                '<comment>Options:</comment>',
                '  <info>-h, --help</info>             Display this help message',
                '  <info>-q, --quiet</info>            Do not output any message',
                '  <info>-V, --version</info>          Display this application version',
                '  <info>-n, --no-interaction</info>   Do not ask any interactive question',
                '      <info>--ansi</info>             Force ANSI output',
                '      <info>--no-ansi</info>          Disable ANSI output',
                '  <info>-v|vv|vvv, --verbose</info>   Increase the verbosity of messages',
                '',

        ]);
    }
}
