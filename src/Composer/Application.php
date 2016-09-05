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
use sebastianfeldmann\CaptainHook\Console\IO\ComposerIO;

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
     * IO Getter.
     *
     * @return \sebastianfeldmann\CaptainHook\Console\IO\ComposerIO
     */
    public function getIO()
    {
        return $this->io;
    }
}
