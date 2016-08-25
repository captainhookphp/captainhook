<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Command;

use HookMeUp\Config\Factory;
use HookMeUp\Console\IO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Base
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Base extends Command
{
    /**
     * @var \HookMeUp\Console\IO
     */
    private $io;

    /**
     * @var \HookMeUp\Config
     */
    private $config;

    /**
     * @param \HookMeUp\Console\IO $io
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
     * @return \HookMeUp\Console\IO
     */
    public function getIO(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->io) {
            $this->io = new IO\DefaultIO($input, $output, $this->getHelperSet());
        }
        return $this->io;
    }

    /**
     * @param  string $path
     * @param  bool   $failIfNotFound
     * @return \HookMeUp\Config
     */
    protected function getConfig($path = null, $failIfNotFound = false)
    {
        $this->config = Factory::create($path);

        if ($failIfNotFound && !$this->config->isLoadedFromFile()) {
            throw new \RuntimeException(
                'Please create a hookmeup configuration first' . PHP_EOL .
                'Run \'hookmeup configure\'' . PHP_EOL .
                'If you have a configuration located elsewhere use the --configuration option'
            );
        }

        return $this->config;
    }
}
