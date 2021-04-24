<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Plugin;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Runner\Hook as RunnerHook;
use SebastianFeldmann\Git\Repository;

/**
 * Runner plugin interface
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.9.0.
 */
abstract class Runner implements CaptainHook
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var IO
     */
    protected $io;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var Config\Plugin
     */
    protected $plugin;

    public function configure(Config $config, IO $io, Repository $repository, Config\Plugin $plugin): void
    {
        $this->config = $config;
        $this->io = $io;
        $this->repository = $repository;
        $this->plugin = $plugin;
    }

    /**
     * Execute before all actions
     */
    abstract public function beforeHook(RunnerHook $hook): void;

    /**
     * Execute before each action
     */
    abstract public function beforeAction(RunnerHook $hook, Config\Action $action): void;

    /**
     * Execute after each action
     */
    abstract public function afterAction(RunnerHook $hook, Config\Action $action): void;

    /**
     * Execute after all actions
     */
    abstract public function afterHook(RunnerHook $hook): void;
}
