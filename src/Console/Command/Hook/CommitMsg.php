<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console\Command\Hook;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\Command\Hook;
use SebastianFeldmann\Git;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommitMessage
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
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
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \SebastianFeldmann\CaptainHook\Config             $config
     * @param \SebastianFeldmann\Git\Repository                 $repository
     */
    protected function setup(InputInterface $input, OutputInterface $output, Config $config, Git\Repository $repository)
    {
        $gitConfig        = $repository->getConfigOperator();
        $commentCharacter = $gitConfig->getSafely('core.commentchar', '#');

        $repository->setCommitMsg(Git\CommitMessage::createFromFile($input->getFirstArgument(), $commentCharacter));
    }
}
