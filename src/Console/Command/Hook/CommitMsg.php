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
 * Class CommitMessage
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class CommitMsg extends Hook
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('commit-msg')
             ->setDescription('Run git commit-msg hook.')
             ->setHelp("This command executes the commit-msg hook.")
             ->addArgument('file', InputArgument::REQUIRED, 'File containing the commit message.');
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
        $repository->setCommitMsg($this->getCommitMsg($input->getFirstArgument()));

        //print_r($repository); exit(1);

        $hook = new Runner\Hook($io, $config, $repository);
        $hook->setHook('commit-msg');
        $hook->run();
    }

    /**
     * Return written commit message.
     *
     * @param  string $path
     * @return \HookMeUp\Git\CommitMessage
     */
    protected function getCommitMsg($path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException('Commit message file not found');
        }
        return new Git\CommitMessage(file_get_contents($path));
    }
}
