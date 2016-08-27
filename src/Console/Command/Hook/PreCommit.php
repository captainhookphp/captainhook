<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Command\Hook;

use HookMeUp\Console\Command\Hook;
use HookMeUp\Git;
use HookMeUp\Runner;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PreCommit
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class PreCommit extends Hook
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('pre-commit')
             ->setDescription('Run git pre-commit hook.')
             ->setHelp("This command executes the pre-commit hook.");
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
        $config = $this->getConfig($this->configFile, true);

        $repository = new Git\Repository($this->repositoryPath);

        $hook = new Runner\Hook($io, $config, $repository);
        $hook->setHook('pre-commit');
        $hook->run();
    }
}
