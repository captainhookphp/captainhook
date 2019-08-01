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
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Runner\Installer;
use RuntimeException;
use SebastianFeldmann\Git\Repository;
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
class Install extends Base
{
    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setName('install')
             ->setDescription('Install git hooks')
             ->setHelp('This command will install the git hooks to your .git directory')
             ->addArgument('hook', InputArgument::OPTIONAL, 'Hook you want to install')
             ->addOption(
                 'configuration',
                 'c',
                 InputOption::VALUE_OPTIONAL,
                 'Path to your json configuration',
                 getcwd() . DIRECTORY_SEPARATOR . CH::CONFIG
             )
             ->addOption(
                 'force',
                 'f',
                 InputOption::VALUE_NONE,
                 'Force to overwrite existing hooks'
             )
             ->addOption(
                 'git-directory',
                 'g',
                 InputOption::VALUE_OPTIONAL,
                 'Path to your .git directory',
                 ''
             )
             ->addOption(
                 'run-mode',
                 'r',
                 InputOption::VALUE_OPTIONAL,
                 'Git hook run mode [local|docker]',
                 Template::LOCAL
             )
             ->addOption(
                 'command',
                 null,
                 InputOption::VALUE_OPTIONAL,
                 'The Docker command to start your container e.g. \'docker exec CONTAINER\''
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

        // figure out where the git repository is located, setting needs to include the '.git' directory
        // the cli option supersedes all other settings
        //  1. command option --git-directory
        //  2. captainhook.json config, git-directory value
        //  3. default current working directory
        $gitDirOption = IOUtil::argToString($input->getOption('git-directory'));
        $gitDir       = !empty($gitDirOption) ? $gitDirOption : $config->getGitDirectory();
        $repo         = new Repository(dirname($gitDir));

        $runMode = IOUtil::argToString($input->getOption('run-mode'));
        $command = IOUtil::argToString($input->getOption('command'));

        if ($runMode === Template::DOCKER && empty($command)) {
            throw new RuntimeException(
                'Option "command" missing for run-mode docker.'
            );
        }

        $installer = new Installer($io, $config, $repo);
        $installer->setForce(IOUtil::argToBool($input->getOption('force')))
                  ->setHook(IOUtil::argToString($input->getArgument('hook')))
                  ->setTemplate(Template\Builder::build($input, $config, $repo))
                  ->run();

        return 0;
    }
}
