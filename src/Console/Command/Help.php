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

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputInterface;
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
             ->setHelp('Shows command instructions')
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
        if ($this->command !== null) {
            $helper = new DescriptorHelper();
            $helper->describe($output, $this->command);
            return;
        }

        $io = $this->getIO($input, $output);
        $io->write($this->getHelpLines());
    }

    /**
     * Return all lines of hel message.
     *
     * @return array
     */
    private function getHelpLines() : array
    {
        return array_merge($this->getVersion(), $this->getCommandList(), $this->getOptions());
    }

    /**
     * Return help rows for version and basic usage.
     *
     * @return array
     */
    private function getVersion() : array
    {
        return [
            $this->getApplication()->getLongVersion(),
            '',
            '<comment>Usage:</comment>',
            '  captainhook [command] [options]',
            ''
        ];
    }

    /**
     * Return help rows listing all commands.
     *
     * @return array
     */
    private function getCommandList() : array
    {
        return [
            '<comment>Available commands:</comment>',
            '  <info>help</info>      Outputs this help message',
            '  <info>configure</info> Create a CaptainHook configuration',
            '  <info>install</info>   Install hooks to your .git/hooks directory',
            ''
        ];
    }

    /**
     * Return help rows describing all options.
     *
     * @return array
     */
    private function getOptions() : array
    {
        return [
            '<comment>Options:</comment>',
            '  <info>-h, --help</info>             Display this help message',
            '  <info>-q, --quiet</info>            Do not output any message',
            '  <info>-V, --version</info>          Display this application version',
            '  <info>-n, --no-interaction</info>   Do not ask any interactive question',
            '      <info>--ansi</info>             Force ANSI output',
            '      <info>--no-ansi</info>          Disable ANSI output',
            '  <info>-v|vv|vvv, --verbose</info>   Increase the verbosity of messages',
            ''
        ];
    }
}
