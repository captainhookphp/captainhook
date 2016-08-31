<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Console\Command\Hook;

use HookMeUp\App\Config;
use HookMeUp\App\Console\Command\Hook;
use HookMeUp\App\Git;
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
     * Hook to execute.
     *
     * @var string
     */
    protected $hookName = 'commit-msg';

    /**
     * Configure the command.
     */
    protected function configure()
    {
        parent::configure();
        $this->addArgument('file', InputArgument::REQUIRED, 'File containing the commit message.');
    }

    /**
     * Read the commit message from file.
     *
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \HookMeUp\App\Config                                  $config
     * @param \HookMeUp\App\Git\Repository                          $repository
     * @internal param \HookMeUp\App\Console\Command\Hook\IO $io
     */
    protected function setup(InputInterface $input, OutputInterface $output, Config $config, Git\Repository $repository)
    {
        $repository->setCommitMsg(Git\CommitMessage::createFromFile($input->getFirstArgument()));
    }
}
