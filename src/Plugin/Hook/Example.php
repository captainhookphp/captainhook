<?php

declare(strict_types=1);

namespace CaptainHook\App\Plugin\Hook;

use CaptainHook\App\Config;
use CaptainHook\App\Plugin;
use CaptainHook\App\Runner\Hook as RunnerHook;

class Example extends Base implements Plugin\Hook
{
    /**
     * Runs before the hook.
     *
     * @param RunnerHook $hook This is the current hook that's running.
     */
    public function beforeHook(RunnerHook $hook): void
    {
        $this->io->write(['<fg=magenta>Plugin ' . self::class . '::beforeHook()</>', '']);
    }

    /**
     * Runs before each action.
     *
     * @param RunnerHook $hook This is the current hook that's running.
     * @param Config\Action $action This is the configuration for action that will
     *                              run immediately following this method.
     */
    public function beforeAction(RunnerHook $hook, Config\Action $action): void
    {
        $this->io->write(['', '   <fg=cyan>Plugin ' . self::class . '::beforeAction()</>']);
    }

    /**
     * Runs after each action.
     *
     * @param RunnerHook $hook This is the current hook that's running.
     * @param Config\Action $action This is the configuration for action that just
     *                              ran immediately before this method.
     */
    public function afterAction(RunnerHook $hook, Config\Action $action): void
    {
        $this->io->write(['', '   <fg=cyan>Plugin ' . self::class . '::afterAction()</>', '']);
    }

    /**
     * Runs after the hook.
     *
     * @param RunnerHook $hook This is the current hook that's running.
     */
    public function afterHook(RunnerHook $hook): void
    {
        $this->io->write(['', '<fg=magenta>Plugin ' . self::class . '::afterHook()</>']);
    }
}
