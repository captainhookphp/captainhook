<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook;

use SebastianFeldmann\CaptainHook\Console\IO;

/**
 * Class Runner
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Runner
{
    /**
     * @var \SebastianFeldmann\CaptainHook\Console\IO
     */
    protected $io;

    /**
     * @var \SebastianFeldmann\CaptainHook\Config
     */
    protected $config;

    /**
     * Installer constructor.
     *
     * @param \SebastianFeldmann\CaptainHook\Console\IO $io
     * @param \SebastianFeldmann\CaptainHook\Config     $config
     */
    public function __construct(IO $io, Config $config)
    {
        $this->io     = $io;
        $this->config = $config;
    }

    /**
     * Executes the Runner.
     */
    abstract public function run();
}
