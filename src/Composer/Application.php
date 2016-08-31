<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Composer;

use Composer\IO\IOInterface;
use sebastianfeldmann\CaptainHook\Console\Application\ConfigHandler;
use sebastianfeldmann\CaptainHook\Console\Command\Configuration;
use sebastianfeldmann\CaptainHook\Console\IO\ComposerIO;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Application extends ConfigHandler
{
    /**
     * Composer IO Proxy
     *
     * @var \sebastianfeldmann\CaptainHook\Console\IO\ComposerIO
     */
    protected $io;

    /**
     * Set the composer application IO.
     *
     * @param  \Composer\IO\IOInterface $io
     */
    public function setProxyIO(IOInterface $io)
    {
        $this->io = new ComposerIO($io);
    }

    /**
     * Execute hook.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $input   = new ArrayInput(['--configuration' => $this->getConfigFile()]);
        $command = new Configuration();
        $command->setIO($this->io);
        return $command->run($input, $output);
    }
}
