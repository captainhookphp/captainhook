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

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Base
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Base extends Command
{
    /**
     * Input output handler.
     *
     * @var \CaptainHook\App\Console\IO
     */
    private $io;

    /**
     * CaptainHook configuration
     *
     * @var \CaptainHook\App\Config
     */
    private $config;

    /**
     * IO setter.
     *
     * @param \CaptainHook\App\Console\IO $io
     */
    public function setIO(IO $io)
    {
        $this->io = $io;
    }

    /**
     * IO interface getter.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return \CaptainHook\App\Console\IO
     */
    public function getIO(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->io) {
            $this->io = new IO\DefaultIO($input, $output, $this->getHelperSet());
        }
        return $this->io;
    }

    /**
     * CaptainHook config getter.
     *
     * @param  string $path
     * @param  bool   $failIfNotFound
     * @return \CaptainHook\App\Config
     */
    protected function getConfig($path = null, $failIfNotFound = false)
    {
        $this->config = Config\Factory::create($path);

        if ($failIfNotFound && !$this->config->isLoadedFromFile()) {
            throw new \RuntimeException(
                'Please create a captainhook configuration first' . PHP_EOL .
                'Run \'captainhook configure\'' . PHP_EOL .
                'If you have a configuration located elsewhere use the --configuration option'
            );
        }
        return $this->config;
    }
}
