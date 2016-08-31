<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook;

use CaptainHook\Console\IO;
use CaptainHook\Git\Repository;

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
     * @var \CaptainHook\Console\IO|\CaptainHook\IO
     */
    protected $io;

    /**
     * @var \CaptainHook\Config
     */
    protected $config;

    /**
     * @var \CaptainHook\Git\Repository
     */
    protected $repository;

    /**
     * Installer constructor.
     *
     * @param \CaptainHook\Console\IO     $io
     * @param \CaptainHook\Config         $config
     * @param \CaptainHook\Git\Repository $repository
     */
    public function __construct(IO $io, Config $config, Repository $repository)
    {
        $this->io         = $io;
        $this->config     = $config;
        $this->repository = $repository;
    }

    /**
     * Executes the Runner.
     */
    abstract public function run();
}
