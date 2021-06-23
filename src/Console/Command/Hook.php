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

use CaptainHook\App\Config;
use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Hook\Util;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Hook extends RepositoryAware
{
    /**
     * Name of the hook to execute
     *
     * @var string
     */
    protected $hookName;

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();
        $this->setName('hook:' . $this->hookName)
             ->setDescription('Run git ' . $this->hookName . ' hook.')
             ->setHelp('This command executes the ' . $this->hookName . ' hook.');

        $this->addOption(
            'bootstrap',
            'b',
            InputOption::VALUE_OPTIONAL,
            'Relative path from your config file to your bootstrap file'
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io         = $this->getIO($input, $output);
        $config     = $this->createConfig($input, true, ['git-directory', 'bootstrap']);
        $repository = $this->createRepository(dirname($config->getGitDirectory()));

        // use ansi coloring if not disabled in captainhook.json
        $output->setDecorated($config->useAnsiColors());
        // use the configured verbosity to manage general output verbosity
        $output->setVerbosity(IOUtil::mapConfigVerbosity($config->getVerbosity()));

        try {
            $this->handleBootstrap($config);

            $class = '\\CaptainHook\\App\\Runner\\Hook\\' . Util::getHookCommand($this->hookName);
            /** @var \CaptainHook\App\Runner\Hook $hook */
            $hook  = new $class($io, $config, $repository);
            $hook->run();
            return 0;
        } catch (Exception $e) {
            if ($output->isDebug()) {
                throw $e;
            }
            return $this->handleError($output, $e);
        }
    }

    /**
     * If CaptainHook is executed via PHAR this handles the bootstrap file inclusion
     *
     * @param \CaptainHook\App\Config $config
     */
    private function handleBootstrap(Config $config): void
    {
        if ($this->resolver->isPharRelease()) {
            $bootstrapFile = dirname($config->getPath()) . '/' . $config->getBootstrap();
            if (!file_exists($bootstrapFile)) {
                throw new RuntimeException('bootstrap file not found');
            }
            try {
                require $bootstrapFile;
            } catch (Throwable $t) {
                throw new RuntimeException(
                    'Bootstrapping failed:' . PHP_EOL .
                    '  Please fix your bootstrap file `' . $bootstrapFile . '`' . PHP_EOL
                );
            }
        }
    }

    /**
     * Handle all hook errors
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @param  \Exception                                        $e
     * @return int
     */
    private function handleError(OutputInterface $output, Exception $e): int
    {
        $header = PHP_EOL . IOUtil::getLineSeparator() . PHP_EOL
                . IOUtil::formatHeadline(get_class($e), 80, '>', '<') . PHP_EOL
                . IOUtil::getLineSeparator();

        $output->writeLn($header, OutputInterface::VERBOSITY_VERBOSE);
        $output->writeLn(PHP_EOL . $e->getMessage());

        return 1;
    }
}
