<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\Command;

use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Runner\Uninstaller;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Uninstall
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.17.0
 */
class Uninstall extends RepositoryAware
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('uninstall')
             ->setDescription('Uninstall git hooks')
             ->setHelp('This command will remove the git hooks from your .git directory')
             ->addArgument(
                 'hook',
                 InputArgument::OPTIONAL,
                 'Limit the hook you want to uninstall. By default all hooks get uninstalled.'
             )
             ->addOption(
                 'move-existing-to',
                 null,
                 InputOption::VALUE_OPTIONAL,
                 'Move existing hooks to given directory'
             );
    }

    /**
     * Execute the command
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io     = $this->getIO($input, $output);
        $config = $this->createConfig($input, true, ['git-directory']);
        $repo   = $this->createRepository(dirname($config->getGitDirectory()));

        // use the configured verbosity to manage general output verbosity
        $output->setVerbosity(IOUtil::mapConfigVerbosity($config->getVerbosity()));

        $uninstaller = new Uninstaller($io, $config, $repo);
        $uninstaller->setMoveExistingTo(IOUtil::argToString($input->getOption('move-existing-to')))
                    ->setHook(IOUtil::argToString($input->getArgument('hook')))
                    ->run();
        return 0;
    }
}
