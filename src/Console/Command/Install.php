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
use CaptainHook\App\Console\Runtime\Resolver;
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Runner\Installer;
use RuntimeException;
use SebastianFeldmann\Camino\Check;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Install
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Install extends RepositoryAware
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('install')
             ->setDescription('Install git hooks')
             ->setHelp('This command will install the git hooks to your .git directory')
             ->addArgument(
                 'hook',
                 InputArgument::OPTIONAL,
                 'Limit the hook you want to install. By default all hooks get installed.'
             )
             ->addOption(
                 'force',
                 'f',
                 InputOption::VALUE_NONE,
                 'Force install without confirmation'
             )
             ->addOption(
                 'skip-existing',
                 's',
                 InputOption::VALUE_NONE,
                 'Do not overwrite existing hooks'
             )
             ->addOption(
                 'move-existing-to',
                 null,
                 InputOption::VALUE_OPTIONAL,
                 'Move existing hooks to given directory'
             )
             ->addOption(
                 'bootstrap',
                 'b',
                 InputOption::VALUE_OPTIONAL,
                 'Path to composers vendor/autoload.php'
             )
             ->addOption(
                 'run-mode',
                 'm',
                 InputOption::VALUE_OPTIONAL,
                 'Git hook run mode [php|shell|docker]'
             )
             ->addOption(
                 'run-exec',
                 'e',
                 InputOption::VALUE_OPTIONAL,
                 'The Docker command to start your container e.g. \'docker exec CONTAINER\''
             )
             ->addOption(
                 'run-path',
                 'p',
                 InputOption::VALUE_OPTIONAL,
                 'The path to the CaptainHook executable \'/usr/bin/captainhook\''
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
        $config = $this->createConfig($input, true, ['git-directory', 'run-mode', 'run-exec', 'run-path', 'bootstrap']);
        $repo   = $this->createRepository(dirname($config->getGitDirectory()));

        // use the configured verbosity to manage general output verbosity
        $output->setVerbosity(IOUtil::mapConfigVerbosity($config->getVerbosity()));

        if ($config->getRunMode() === Template::DOCKER && empty($config->getRunExec())) {
            throw new RuntimeException(
                'Option "run-exec" missing for run-mode docker.'
            );
        }

        $template  = Template\Builder::build($config, $repo, $this->resolver);
        $installer = new Installer($io, $config, $repo, $template);
        $installer->setForce(IOUtil::argToBool($input->getOption('force')))
                  ->setSkipExisting(IOUtil::argToBool($input->getOption('skip-existing')))
                  ->setMoveExistingTo(IOUtil::argToString($input->getOption('move-existing-to')))
                  ->setHook(IOUtil::argToString($input->getArgument('hook')))
                  ->run();

        return 0;
    }
}
