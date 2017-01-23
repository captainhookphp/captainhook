<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Composer;

use Composer\IO\IOInterface;
use SebastianFeldmann\CaptainHook\Console\Application\ConfigHandler;
use SebastianFeldmann\CaptainHook\Console\IO\ComposerIO;

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
     * @var \SebastianFeldmann\CaptainHook\Console\IO\ComposerIO
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
     * IO Getter.
     *
     * @return \SebastianFeldmann\CaptainHook\Console\IO\ComposerIO
     */
    public function getIO() : ComposerIO
    {
        return $this->io;
    }
}
