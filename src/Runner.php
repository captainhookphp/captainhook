<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App;

use CaptainHook\App\Console\IO;
use CaptainHook\App\Git\Repository;

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
     * @var \CaptainHook\App\Console\IO|\CaptainHook\App\IO
     */
    protected $io;

    /**
     * @var \CaptainHook\App\Config
     */
    protected $config;

    /**
     * @var \CaptainHook\App\Git\Repository
     */
    protected $repository;

    /**
     * Installer constructor.
     *
     * @param \CaptainHook\App\Console\IO     $io
     * @param \CaptainHook\App\Config         $config
     * @param \CaptainHook\App\Git\Repository $repository
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
